# CrewHub データベース設計

## ER図（概要）

```
┌─────────────┐       ┌─────────────┐       ┌─────────────┐
│   users     │       │   clients   │       │  projects   │
│─────────────│       │─────────────│       │─────────────│
│ id          │       │ id          │       │ id          │
│ email       │       │ name        │◄──────│ client_id   │
│ role        │       │ ...         │       │ title       │
│ ...         │       └─────────────┘       │ ...         │
└──────┬──────┘                             └──────┬──────┘
       │                                           │
       │ 1:1                                       │ 1:N
       ▼                                           ▼
┌─────────────┐                             ┌─────────────┐
│staff_profiles│                            │   shifts    │
│─────────────│                             │─────────────│
│ user_id     │                             │ project_id  │
│ phone       │                             │ date        │
│ address     │                             │ start_time  │
│ bank_account│                             │ ...         │
│ ...         │                             └──────┬──────┘
└─────────────┘                                    │
       │                                           │ 1:N
       │ N:M                                       ▼
       ▼                                    ┌─────────────┐
┌─────────────┐                             │shift_       │
│ user_skills │                             │applications │
│─────────────│                             │─────────────│
│ user_id     │                             │ shift_id    │
│ skill_id    │                             │ user_id     │
└─────────────┘                             │ status      │
       │                                    └─────────────┘
       │ N:1                                       │
       ▼                                           │
┌─────────────┐                                    │
│   skills    │                                    │
│─────────────│                                    │
│ id          │                                    │
│ name        │                                    │
└─────────────┘                                    │
                                                   │
┌─────────────┐       ┌─────────────┐             │
│ attendances │◄──────│   users     │◄────────────┘
│─────────────│       └─────────────┘
│ user_id     │
│ shift_id    │
│ clock_in    │
│ clock_out   │
└─────────────┘

┌─────────────┐       ┌─────────────┐       ┌─────────────┐
│ chat_rooms  │───────│chat_room_   │───────│   users     │
│─────────────│       │members      │       └─────────────┘
│ id          │       │─────────────│
│ project_id  │       │ room_id     │
│ type        │       │ user_id     │
└──────┬──────┘       └─────────────┘
       │
       │ 1:N
       ▼
┌─────────────┐
│  messages   │
│─────────────│
│ room_id     │
│ user_id     │
│ content     │
└─────────────┘
```

## テーブル定義

### 1. users（ユーザー）

全ユーザー共通のアカウント情報。

| カラム | 型 | NULL | 説明 |
|--------|-----|------|------|
| id | BIGINT | NO | PK |
| email | VARCHAR(255) | NO | メールアドレス（UNIQUE） |
| password | VARCHAR(255) | NO | パスワードハッシュ |
| role | ENUM | NO | 'staff', 'client', 'manager', 'admin' |
| name | VARCHAR(255) | NO | 氏名 |
| email_verified_at | TIMESTAMP | YES | メール認証日時 |
| status | ENUM | NO | 'pending', 'active', 'suspended' |
| remember_token | VARCHAR(100) | YES | |
| created_at | TIMESTAMP | NO | |
| updated_at | TIMESTAMP | NO | |

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('staff', 'client', 'manager', 'admin') NOT NULL DEFAULT 'staff',
    name VARCHAR(255) NOT NULL,
    email_verified_at TIMESTAMP NULL,
    status ENUM('pending', 'active', 'suspended') NOT NULL DEFAULT 'pending',
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_role (role),
    INDEX idx_status (status)
);
```

### 2. staff_profiles（スタッフプロフィール）

スタッフ固有の詳細情報。

| カラム | 型 | NULL | 説明 |
|--------|-----|------|------|
| id | BIGINT | NO | PK |
| user_id | BIGINT | NO | FK → users |
| phone | VARCHAR(20) | YES | 電話番号 |
| date_of_birth | DATE | YES | 生年月日 |
| gender | ENUM | YES | 'male', 'female', 'other' |
| postal_code | VARCHAR(10) | YES | 郵便番号 |
| prefecture | VARCHAR(50) | YES | 都道府県 |
| city | VARCHAR(100) | YES | 市区町村 |
| address | VARCHAR(255) | YES | 番地以降 |
| bank_name | VARCHAR(100) | YES | 銀行名 |
| bank_branch | VARCHAR(100) | YES | 支店名 |
| bank_account_type | ENUM | YES | 'ordinary', 'current' |
| bank_account_number | VARCHAR(20) | YES | 口座番号 |
| bank_account_holder | VARCHAR(100) | YES | 口座名義 |
| profile_photo_path | VARCHAR(255) | YES | プロフィール写真パス |
| id_verified_at | TIMESTAMP | YES | 本人確認完了日時 |
| bio | TEXT | YES | 自己紹介 |
| created_at | TIMESTAMP | NO | |
| updated_at | TIMESTAMP | NO | |

```sql
CREATE TABLE staff_profiles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL UNIQUE,
    phone VARCHAR(20) NULL,
    date_of_birth DATE NULL,
    gender ENUM('male', 'female', 'other') NULL,
    postal_code VARCHAR(10) NULL,
    prefecture VARCHAR(50) NULL,
    city VARCHAR(100) NULL,
    address VARCHAR(255) NULL,
    bank_name VARCHAR(100) NULL,
    bank_branch VARCHAR(100) NULL,
    bank_account_type ENUM('ordinary', 'current') NULL,
    bank_account_number VARCHAR(20) NULL,
    bank_account_holder VARCHAR(100) NULL,
    profile_photo_path VARCHAR(255) NULL,
    id_verified_at TIMESTAMP NULL,
    bio TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### 3. clients（クライアント/企業）

イベント主催企業・依頼元の情報。

| カラム | 型 | NULL | 説明 |
|--------|-----|------|------|
| id | BIGINT | NO | PK |
| name | VARCHAR(255) | NO | 企業名 |
| contact_person | VARCHAR(255) | YES | 担当者名 |
| email | VARCHAR(255) | NO | 連絡先メール |
| phone | VARCHAR(20) | YES | 電話番号 |
| postal_code | VARCHAR(10) | YES | 郵便番号 |
| address | VARCHAR(500) | YES | 住所 |
| status | ENUM | NO | 'active', 'inactive' |
| notes | TEXT | YES | 備考 |
| created_at | TIMESTAMP | NO | |
| updated_at | TIMESTAMP | NO | |

```sql
CREATE TABLE clients (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    contact_person VARCHAR(255) NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NULL,
    postal_code VARCHAR(10) NULL,
    address VARCHAR(500) NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    notes TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status)
);
```

### 4. projects（案件）

イベント案件の情報。

| カラム | 型 | NULL | 説明 |
|--------|-----|------|------|
| id | BIGINT | NO | PK |
| client_id | BIGINT | NO | FK → clients |
| created_by | BIGINT | NO | FK → users（作成者） |
| title | VARCHAR(255) | NO | 案件名 |
| description | TEXT | YES | 詳細説明 |
| venue_name | VARCHAR(255) | YES | 会場名 |
| venue_address | VARCHAR(500) | YES | 会場住所 |
| start_date | DATE | NO | 開始日 |
| end_date | DATE | NO | 終了日 |
| hourly_wage | INT | NO | 時給（円） |
| transportation_fee | INT | YES | 交通費（円） |
| requirements | TEXT | YES | 必要条件・持ち物 |
| status | ENUM | NO | 'draft', 'published', 'closed', 'completed' |
| created_at | TIMESTAMP | NO | |
| updated_at | TIMESTAMP | NO | |

```sql
CREATE TABLE projects (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    client_id BIGINT UNSIGNED NOT NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    venue_name VARCHAR(255) NULL,
    venue_address VARCHAR(500) NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    hourly_wage INT UNSIGNED NOT NULL,
    transportation_fee INT UNSIGNED NULL DEFAULT 0,
    requirements TEXT NULL,
    status ENUM('draft', 'published', 'closed', 'completed') NOT NULL DEFAULT 'draft',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_status (status),
    INDEX idx_dates (start_date, end_date)
);
```

### 5. shifts（シフト）

案件内の個別シフト枠。

| カラム | 型 | NULL | 説明 |
|--------|-----|------|------|
| id | BIGINT | NO | PK |
| project_id | BIGINT | NO | FK → projects |
| date | DATE | NO | シフト日 |
| start_time | TIME | NO | 開始時刻 |
| end_time | TIME | NO | 終了時刻 |
| break_minutes | INT | NO | 休憩時間（分） |
| capacity | INT | NO | 募集人数 |
| confirmed_count | INT | NO | 確定人数 |
| notes | TEXT | YES | 備考 |
| created_at | TIMESTAMP | NO | |
| updated_at | TIMESTAMP | NO | |

```sql
CREATE TABLE shifts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id BIGINT UNSIGNED NOT NULL,
    date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    break_minutes INT UNSIGNED NOT NULL DEFAULT 0,
    capacity INT UNSIGNED NOT NULL DEFAULT 1,
    confirmed_count INT UNSIGNED NOT NULL DEFAULT 0,
    notes TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    INDEX idx_date (date),
    INDEX idx_project_date (project_id, date)
);
```

### 6. shift_applications（シフト応募）

スタッフのシフト応募状況。

| カラム | 型 | NULL | 説明 |
|--------|-----|------|------|
| id | BIGINT | NO | PK |
| shift_id | BIGINT | NO | FK → shifts |
| user_id | BIGINT | NO | FK → users |
| status | ENUM | NO | 'pending', 'approved', 'rejected', 'cancelled' |
| applied_at | TIMESTAMP | NO | 応募日時 |
| processed_at | TIMESTAMP | YES | 処理日時 |
| processed_by | BIGINT | YES | FK → users（処理者） |
| notes | TEXT | YES | 備考 |
| created_at | TIMESTAMP | NO | |
| updated_at | TIMESTAMP | NO | |

```sql
CREATE TABLE shift_applications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    shift_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    status ENUM('pending', 'approved', 'rejected', 'cancelled') NOT NULL DEFAULT 'pending',
    applied_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    processed_at TIMESTAMP NULL,
    processed_by BIGINT UNSIGNED NULL,
    notes TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (shift_id) REFERENCES shifts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (processed_by) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY uk_shift_user (shift_id, user_id),
    INDEX idx_status (status),
    INDEX idx_user_status (user_id, status)
);
```

### 7. attendances（勤怠）

勤怠打刻・実績データ。

| カラム | 型 | NULL | 説明 |
|--------|-----|------|------|
| id | BIGINT | NO | PK |
| user_id | BIGINT | NO | FK → users |
| shift_id | BIGINT | NO | FK → shifts |
| clock_in | TIMESTAMP | YES | 出勤打刻 |
| clock_out | TIMESTAMP | YES | 退勤打刻 |
| break_minutes | INT | YES | 実休憩時間（分） |
| status | ENUM | NO | 'pending', 'approved', 'rejected' |
| work_report | TEXT | YES | 業務報告 |
| approved_by | BIGINT | YES | FK → users |
| approved_at | TIMESTAMP | YES | 承認日時 |
| created_at | TIMESTAMP | NO | |
| updated_at | TIMESTAMP | NO | |

```sql
CREATE TABLE attendances (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    shift_id BIGINT UNSIGNED NOT NULL,
    clock_in TIMESTAMP NULL,
    clock_out TIMESTAMP NULL,
    break_minutes INT UNSIGNED NULL,
    status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    work_report TEXT NULL,
    approved_by BIGINT UNSIGNED NULL,
    approved_at TIMESTAMP NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (shift_id) REFERENCES shifts(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY uk_user_shift (user_id, shift_id),
    INDEX idx_status (status)
);
```

### 8. attendance_photos（勤怠写真）

勤怠報告に添付された写真。

| カラム | 型 | NULL | 説明 |
|--------|-----|------|------|
| id | BIGINT | NO | PK |
| attendance_id | BIGINT | NO | FK → attendances |
| file_path | VARCHAR(255) | NO | ファイルパス |
| type | ENUM | NO | 'clock_in', 'clock_out', 'report' |
| created_at | TIMESTAMP | NO | |

```sql
CREATE TABLE attendance_photos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    attendance_id BIGINT UNSIGNED NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    type ENUM('clock_in', 'clock_out', 'report') NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (attendance_id) REFERENCES attendances(id) ON DELETE CASCADE
);
```

### 9. skills（スキル・資格）

スタッフが登録可能なスキル・資格のマスタ。

| カラム | 型 | NULL | 説明 |
|--------|-----|------|------|
| id | BIGINT | NO | PK |
| name | VARCHAR(100) | NO | スキル名 |
| category | VARCHAR(50) | YES | カテゴリ |
| created_at | TIMESTAMP | NO | |
| updated_at | TIMESTAMP | NO | |

```sql
CREATE TABLE skills (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_name (name)
);
```

### 10. user_skills（ユーザースキル）

ユーザーとスキルの中間テーブル。

| カラム | 型 | NULL | 説明 |
|--------|-----|------|------|
| id | BIGINT | NO | PK |
| user_id | BIGINT | NO | FK → users |
| skill_id | BIGINT | NO | FK → skills |
| verified | BOOLEAN | NO | 認定済みフラグ |
| created_at | TIMESTAMP | NO | |

```sql
CREATE TABLE user_skills (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    skill_id BIGINT UNSIGNED NOT NULL,
    verified BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (skill_id) REFERENCES skills(id) ON DELETE CASCADE,
    UNIQUE KEY uk_user_skill (user_id, skill_id)
);
```

### 11. payments（支払）

スタッフへの支払記録。

| カラム | 型 | NULL | 説明 |
|--------|-----|------|------|
| id | BIGINT | NO | PK |
| user_id | BIGINT | NO | FK → users |
| period_start | DATE | NO | 対象期間開始 |
| period_end | DATE | NO | 対象期間終了 |
| base_amount | INT | NO | 基本給（円） |
| transportation_amount | INT | NO | 交通費（円） |
| deduction_amount | INT | NO | 控除額（円） |
| total_amount | INT | NO | 合計支払額（円） |
| status | ENUM | NO | 'pending', 'processing', 'completed' |
| paid_at | TIMESTAMP | YES | 支払日時 |
| created_at | TIMESTAMP | NO | |
| updated_at | TIMESTAMP | NO | |

```sql
CREATE TABLE payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    period_start DATE NOT NULL,
    period_end DATE NOT NULL,
    base_amount INT UNSIGNED NOT NULL DEFAULT 0,
    transportation_amount INT UNSIGNED NOT NULL DEFAULT 0,
    deduction_amount INT UNSIGNED NOT NULL DEFAULT 0,
    total_amount INT UNSIGNED NOT NULL DEFAULT 0,
    status ENUM('pending', 'processing', 'completed') NOT NULL DEFAULT 'pending',
    paid_at TIMESTAMP NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_period (user_id, period_start, period_end),
    INDEX idx_status (status)
);
```

### 12. payment_details（支払明細）

支払の明細（シフトごと）。

| カラム | 型 | NULL | 説明 |
|--------|-----|------|------|
| id | BIGINT | NO | PK |
| payment_id | BIGINT | NO | FK → payments |
| attendance_id | BIGINT | NO | FK → attendances |
| work_hours | DECIMAL(5,2) | NO | 勤務時間 |
| hourly_wage | INT | NO | 時給 |
| amount | INT | NO | 金額 |
| created_at | TIMESTAMP | NO | |

```sql
CREATE TABLE payment_details (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    payment_id BIGINT UNSIGNED NOT NULL,
    attendance_id BIGINT UNSIGNED NOT NULL,
    work_hours DECIMAL(5,2) NOT NULL,
    hourly_wage INT UNSIGNED NOT NULL,
    amount INT UNSIGNED NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (payment_id) REFERENCES payments(id) ON DELETE CASCADE,
    FOREIGN KEY (attendance_id) REFERENCES attendances(id) ON DELETE RESTRICT
);
```

### 13. chat_rooms（チャットルーム）

チャットルーム。

| カラム | 型 | NULL | 説明 |
|--------|-----|------|------|
| id | BIGINT | NO | PK |
| project_id | BIGINT | YES | FK → projects（案件チャットの場合） |
| type | ENUM | NO | 'project', 'direct', 'group' |
| name | VARCHAR(255) | YES | ルーム名（グループの場合） |
| created_at | TIMESTAMP | NO | |
| updated_at | TIMESTAMP | NO | |

```sql
CREATE TABLE chat_rooms (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id BIGINT UNSIGNED NULL,
    type ENUM('project', 'direct', 'group') NOT NULL,
    name VARCHAR(255) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    INDEX idx_type (type)
);
```

### 14. chat_room_members（チャットルームメンバー）

チャットルームの参加者。

| カラム | 型 | NULL | 説明 |
|--------|-----|------|------|
| id | BIGINT | NO | PK |
| chat_room_id | BIGINT | NO | FK → chat_rooms |
| user_id | BIGINT | NO | FK → users |
| last_read_at | TIMESTAMP | YES | 最終既読日時 |
| created_at | TIMESTAMP | NO | |

```sql
CREATE TABLE chat_room_members (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    chat_room_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    last_read_at TIMESTAMP NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (chat_room_id) REFERENCES chat_rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY uk_room_user (chat_room_id, user_id)
);
```

### 15. messages（メッセージ）

チャットメッセージ。

| カラム | 型 | NULL | 説明 |
|--------|-----|------|------|
| id | BIGINT | NO | PK |
| chat_room_id | BIGINT | NO | FK → chat_rooms |
| user_id | BIGINT | NO | FK → users |
| content | TEXT | NO | メッセージ本文 |
| type | ENUM | NO | 'text', 'image', 'file' |
| file_path | VARCHAR(255) | YES | 添付ファイルパス |
| created_at | TIMESTAMP | NO | |

```sql
CREATE TABLE messages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    chat_room_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    content TEXT NOT NULL,
    type ENUM('text', 'image', 'file') NOT NULL DEFAULT 'text',
    file_path VARCHAR(255) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (chat_room_id) REFERENCES chat_rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_room_created (chat_room_id, created_at)
);
```

### 16. notifications（通知）

プッシュ通知・アプリ内通知。

| カラム | 型 | NULL | 説明 |
|--------|-----|------|------|
| id | BIGINT | NO | PK |
| user_id | BIGINT | NO | FK → users |
| type | VARCHAR(50) | NO | 通知タイプ |
| title | VARCHAR(255) | NO | タイトル |
| body | TEXT | NO | 本文 |
| data | JSON | YES | 追加データ |
| read_at | TIMESTAMP | YES | 既読日時 |
| created_at | TIMESTAMP | NO | |

```sql
CREATE TABLE notifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    type VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    data JSON NULL,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_read (user_id, read_at),
    INDEX idx_type (type)
);
```

### 17. device_tokens（デバイストークン）

プッシュ通知用のデバイストークン。

| カラム | 型 | NULL | 説明 |
|--------|-----|------|------|
| id | BIGINT | NO | PK |
| user_id | BIGINT | NO | FK → users |
| token | VARCHAR(255) | NO | FCMトークン |
| platform | ENUM | NO | 'ios', 'android' |
| created_at | TIMESTAMP | NO | |
| updated_at | TIMESTAMP | NO | |

```sql
CREATE TABLE device_tokens (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    token VARCHAR(255) NOT NULL,
    platform ENUM('ios', 'android') NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY uk_token (token)
);
```

### 18. staff_availabilities（稼働可能日）

スタッフの稼働可能日設定。

| カラム | 型 | NULL | 説明 |
|--------|-----|------|------|
| id | BIGINT | NO | PK |
| user_id | BIGINT | NO | FK → users |
| date | DATE | NO | 日付 |
| is_available | BOOLEAN | NO | 稼働可能フラグ |
| notes | VARCHAR(255) | YES | 備考 |
| created_at | TIMESTAMP | NO | |
| updated_at | TIMESTAMP | NO | |

```sql
CREATE TABLE staff_availabilities (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    date DATE NOT NULL,
    is_available BOOLEAN NOT NULL DEFAULT TRUE,
    notes VARCHAR(255) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY uk_user_date (user_id, date),
    INDEX idx_date (date)
);
```

## インデックス設計方針

1. **主キー**: すべてのテーブルに `id` を設定
2. **外部キー**: リレーションシップには外部キー制約を設定
3. **検索頻度の高いカラム**: `status`, `date` などにインデックス
4. **複合インデックス**: 頻繁に組み合わせて検索されるカラム

## 今後の拡張

Phase 2以降で追加予定のテーブル：

- `gps_logs` - GPS位置情報ログ
- `trainings` - 研修マスタ
- `user_trainings` - 研修受講履歴
- `attendees` - イベント参加者
- `tickets` - チケット管理
