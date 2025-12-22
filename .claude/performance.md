# Performance Optimization Guide

## Laravel Backend Performance

### Database Optimization

#### N+1問題の防止
```php
// Bad: N+1クエリ
$users = User::all();
foreach ($users as $user) {
    echo $user->posts->count(); // 毎回クエリが発生
}

// Good: Eager Loading
$users = User::with('posts')->get();
foreach ($users as $user) {
    echo $user->posts->count(); // キャッシュされたデータを使用
}
```

#### インデックス設計
- 頻繁に検索・ソートされるカラムにはインデックスを追加
- 複合インデックスは検索パターンに合わせて設計
- `EXPLAIN`でクエリプランを確認

```php
// マイグレーションでインデックス追加
Schema::table('users', function (Blueprint $table) {
    $table->index('email');
    $table->index(['status', 'created_at']);
});
```

#### クエリ最適化
```php
// Bad: 全カラム取得
$users = User::all();

// Good: 必要なカラムのみ取得
$users = User::select(['id', 'name', 'email'])->get();

// Bad: ループ内でクエリ
foreach ($ids as $id) {
    $user = User::find($id);
}

// Good: 一括取得
$users = User::whereIn('id', $ids)->get()->keyBy('id');
```

### キャッシュ戦略

#### Redisキャッシュ活用
```php
// 設定キャッシュ
Cache::remember('settings', 3600, function () {
    return Setting::all()->pluck('value', 'key');
});

// タグ付きキャッシュ（関連データの一括削除用）
Cache::tags(['users'])->remember("user:{$id}", 3600, function () use ($id) {
    return User::with('profile')->find($id);
});

// キャッシュ無効化
Cache::tags(['users'])->flush();
```

#### キャッシュすべきデータ
- 変更頻度が低い設定データ
- マスターデータ（都道府県、カテゴリなど）
- 集計結果
- 外部API呼び出し結果

### APIレスポンス最適化

#### Resourceクラスでの変換
```php
// UserResource.php
public function toArray($request)
{
    return [
        'id' => $this->id,
        'name' => $this->name,
        // 必要な場合のみリレーション含める
        'posts' => $this->when($request->include_posts, function () {
            return PostResource::collection($this->posts);
        }),
    ];
}
```

#### ページネーション
```php
// 大量データは必ずページネーション
return UserResource::collection(
    User::paginate(20)
);
```

### バックグラウンド処理

#### Queueの活用
```php
// メール送信、通知、重い処理はQueueへ
SendWelcomeEmail::dispatch($user);

// 遅延実行
ProcessPodcast::dispatch($podcast)->delay(now()->addMinutes(10));
```

#### 長時間処理の分割
```php
// チャンク処理で大量データを処理
User::chunk(100, function ($users) {
    foreach ($users as $user) {
        // 処理
    }
});
```

## React Native Frontend Performance

### レンダリング最適化

#### React.memoの活用
```tsx
// 不要な再レンダリングを防止
const UserCard = React.memo(({ user }: { user: User }) => {
    return (
        <View>
            <Text>{user.name}</Text>
        </View>
    );
});
```

#### useMemo/useCallbackの適切な使用
```tsx
// 重い計算はuseMemoでメモ化
const sortedUsers = useMemo(() => {
    return users.sort((a, b) => a.name.localeCompare(b.name));
}, [users]);

// コールバック関数はuseCallbackでメモ化
const handlePress = useCallback(() => {
    navigation.navigate('Detail', { id: user.id });
}, [user.id]);
```

### リスト最適化

#### FlatListの最適化
```tsx
<FlatList
    data={items}
    renderItem={renderItem}
    keyExtractor={(item) => item.id.toString()}
    // パフォーマンス最適化
    initialNumToRender={10}
    maxToRenderPerBatch={10}
    windowSize={5}
    removeClippedSubviews={true}
    getItemLayout={(data, index) => ({
        length: ITEM_HEIGHT,
        offset: ITEM_HEIGHT * index,
        index,
    })}
/>
```

### 画像最適化

#### 適切な画像サイズ
```tsx
// expo-imageを使用
import { Image } from 'expo-image';

<Image
    source={{ uri: imageUrl }}
    style={{ width: 100, height: 100 }}
    contentFit="cover"
    placeholder={blurhash}
    transition={200}
/>
```

### ネットワーク最適化

#### API呼び出しの最適化
```tsx
// React Queryでキャッシュと再取得を管理
const { data, isLoading } = useQuery({
    queryKey: ['users'],
    queryFn: fetchUsers,
    staleTime: 5 * 60 * 1000, // 5分間はキャッシュを使用
});
```

#### データプリフェッチ
```tsx
// 次の画面のデータを事前取得
const prefetchUser = async (userId: string) => {
    await queryClient.prefetchQuery({
        queryKey: ['user', userId],
        queryFn: () => fetchUser(userId),
    });
};
```

### バンドルサイズ最適化

#### 動的インポート
```tsx
// 必要な時にのみロード
const HeavyComponent = lazy(() => import('./HeavyComponent'));

// Suspenseでラップ
<Suspense fallback={<Loading />}>
    <HeavyComponent />
</Suspense>
```

## 監視とプロファイリング

### Laravel

```bash
# クエリログの有効化（開発環境のみ）
DB::enableQueryLog();
// 処理
dd(DB::getQueryLog());

# Laravel Telescopeの使用
php artisan telescope:install
php artisan migrate
```

### React Native

```tsx
// React DevToolsのProfilerを使用
// Flipperでパフォーマンス監視

// 開発環境でのログ
if (__DEV__) {
    console.log('Render count:', renderCount);
}
```

## パフォーマンスチェックリスト

### API開発時
- [ ] N+1クエリが発生していないか？（Eager Loading使用）
- [ ] 必要なカラムのみ取得しているか？
- [ ] 適切なインデックスが設定されているか？
- [ ] 大量データはページネーションしているか？
- [ ] 重い処理はQueueに移動したか？
- [ ] キャッシュ可能なデータはキャッシュしているか？

### フロントエンド開発時
- [ ] 不要な再レンダリングがないか？
- [ ] FlatListは最適化されているか？
- [ ] 画像は適切なサイズか？
- [ ] APIキャッシュは適切か？
- [ ] バンドルサイズは許容範囲か？
