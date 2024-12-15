ALTER DATABASE mydb SET timezone TO 'Asia/Tokyo';
CREATE EXTENSION IF NOT EXISTS pgcrypto;

-- ===============================
-- ユーザー (users) テーブルの作成
-- ===============================
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,            -- id: 主キー
    name VARCHAR(255) NOT NULL,       -- name: 名前
    displayName VARCHAR(255),         -- displayName: 表示名
    email VARCHAR(255) UNIQUE NOT NULL, -- email: メアド (一意制約)
    password VARCHAR(255) NOT NULL,    -- password: ハッシュ化したパスワード
    bio TEXT,                         -- bio: 自己紹介や説明
    image TEXT                        -- image: 画像のパス・base64
);

-- ===============================
-- ルーム (rooms) テーブルの作成
-- ===============================
CREATE TABLE IF NOT EXISTS rooms (
    id SERIAL PRIMARY KEY, -- ルームID (自動インクリメント)
    roomId VARCHAR(32) UNIQUE NOT NULL, -- ルームの一意な識別ID (ルームのURL識別子として使う)
    title VARCHAR(255) NOT NULL, -- ルームのタイトル
    userId INT, -- ルームの作成者 (users.idを参照)
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- ルームの作成日時
    -- 外部キー制約
    CONSTRAINT fk_user_rooms FOREIGN KEY (userId) REFERENCES users (id) ON DELETE SET NULL
);

-- ===============================
-- 投稿 (posts) テーブルの作成
-- ===============================
CREATE TABLE IF NOT EXISTS posts (
    id SERIAL PRIMARY KEY, -- 投稿ID (自動インクリメント)
    roomId VARCHAR(32) NOT NULL, -- どのルームに属するかを識別するためのroomId
    userId INT, -- 投稿者のID (users.idを参照)
    comment TEXT NOT NULL, -- 投稿の内容
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- 作成日時
    -- 外部キー制約
    CONSTRAINT fk_room_posts FOREIGN KEY (roomId) REFERENCES rooms (roomId) ON DELETE CASCADE,
    CONSTRAINT fk_user_posts FOREIGN KEY (userId) REFERENCES users (id) ON DELETE SET NULL
);

-- ===============================
-- 初期データの挿入 (usersテーブル)
-- ===============================
INSERT INTO users (name, displayName, bio, image, email, password)
VALUES
('山田太郎', 'Taro Yamada', '日本のエンジニア。プログラミングが得意です。', 'path/to/image1.jpg', 'taro.yamada@example.com', crypt('password123', gen_salt('bf'))),
('鈴木花子', 'Hanako Suzuki', 'ウェブデザイナー。美しいデザインを作成します。', 'path/to/image2.jpg', 'hanako.suzuki@example.com', crypt('mypassword', gen_salt('bf'))),
('佐藤一郎', 'Ichiro Sato', 'システムアーキテクト。技術的な問題を解決するのが得意です。', 'path/to/image3.jpg', 'ichiro.sato@example.com', crypt('securepass', gen_salt('bf')));

-- ===============================
-- インデックスの作成 (クエリ速度の最適化)
-- ===============================
CREATE INDEX idx_rooms_createdat ON rooms (createdAt DESC); -- ルームの一覧取得用
CREATE INDEX idx_posts_createdat ON posts (createdAt DESC); -- 投稿の一覧取得用
CREATE INDEX idx_posts_roomid ON posts (roomId); -- ルームIDでの投稿検索用
CREATE INDEX idx_rooms_userid ON rooms (userId); -- ルーム作成者の検索用
CREATE INDEX idx_posts_userid ON posts (userId); -- 投稿者の検索用
