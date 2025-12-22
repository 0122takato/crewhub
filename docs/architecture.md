# CrewHub アーキテクチャ設計

## リポジトリ構成

**モノレポ構成**を採用。初期開発のスピードとコード共有の容易さを優先。

```
crewhub/
├── backend/                # Laravel API（共通基盤）
│   ├── app/
│   │   ├── Http/Controllers/
│   │   ├── Models/
│   │   └── Services/
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   └── routes/
│       └── api.php
│
├── frontend/               # React Native（モバイルアプリ）
│   ├── app/                # Expo Router
│   ├── components/
│   ├── hooks/
│   ├── services/
│   └── types/
│
├── admin/                  # 管理者用Web（将来追加）
│
├── packages/               # 共通パッケージ（将来追加）
│   └── shared/             # 共通型定義・ユーティリティ
│
├── docker/                 # Docker設定
├── docs/                   # ドキュメント
└── docker-compose.yml
```

## ユーザー種別とアプリケーション

### ユーザー種別

| 種別 | 説明 | 使用アプリ |
|------|------|-----------|
| スタッフ | イベント現場で働くスタッフ | モバイルアプリ |
| クライアント | イベント主催企業 | モバイルアプリ |
| マネージャー | スタッフ管理者 | モバイルアプリ |
| 管理者 | システム管理者 | 管理画面（Web） |

### アプリケーション構成

1. **モバイルアプリ（frontend/）**
   - スタッフ・クライアント・マネージャーが使用
   - ログイン後、権限に応じて表示を切り替え
   - React Native (Expo)

2. **管理画面（admin/）** ※将来追加
   - 管理者専用のWebダッシュボード
   - React または Laravel Blade

3. **API（backend/）**
   - 共通のRESTful API
   - 認証・認可でアクセス制御

## 将来の拡張

### Phase 2以降で追加予定

- **参加者アプリ**: イベント参加者向け機能
- **GPS機能**: 位置情報トラッキング
- **給与計算**: 自動給与計算システム
- **研修管理**: オンライン研修機能

### 分割検討の基準

以下の状況になったら、リポジトリ分割を検討：

- チーム規模が10人を超え、並行開発で衝突が頻発
- アプリごとにリリースサイクルが大きく異なる
- 参加者アプリが完全に独立したビジネスになる

## 技術スタック

| レイヤー | 技術 |
|---------|------|
| モバイル | React Native, Expo, TypeScript |
| 管理画面 | React (予定) |
| API | Laravel 12, PHP 8.4 |
| データベース | MySQL 8.0 |
| キャッシュ | Redis |
| 認証 | Laravel Sanctum (JWT) |
| リアルタイム | WebSocket / Firebase Cloud Messaging |
| インフラ | Docker |
