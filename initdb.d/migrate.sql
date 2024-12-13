ALTER DATABASE mydb SET timezone TO 'Asia/Tokyo';

-- ===============================
-- ルーム (rooms) テーブルの作成
-- ===============================
CREATE TABLE IF NOT EXISTS rooms (
    id SERIAL PRIMARY KEY, -- ルームID (自動インクリメント)
    roomId VARCHAR(32) UNIQUE NOT NULL, -- ルームの一意な識別ID (ルームのURL識別子として使う)
    title VARCHAR(255) NOT NULL, -- ルームのタイトル
    createdBy VARCHAR(50) NOT NULL, -- ルームの作成者
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- ルームの作成日時
);

-- ===============================
-- 投稿 (posts) テーブルの作成
-- ===============================
CREATE TABLE IF NOT EXISTS posts (
    id SERIAL PRIMARY KEY, -- 投稿ID (自動インクリメント)
    roomId VARCHAR(32) NOT NULL, -- どのルームに属するかを識別するためのroomId
    name VARCHAR(50) NOT NULL, -- 投稿者の名前
    comment TEXT NOT NULL, -- 投稿の内容
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- 作成日時
    -- 外部キー制約
    CONSTRAINT fk_room FOREIGN KEY (roomId) REFERENCES rooms (roomId) ON DELETE CASCADE
);

-- ===============================
-- インデックスの作成 (クエリ速度の最適化)
-- ===============================
CREATE INDEX idx_rooms_createdat ON rooms (createdAt DESC); -- ルームの一覧取得用
CREATE INDEX idx_posts_createdat ON posts (createdAt DESC); -- 投稿の一覧取得用
CREATE INDEX idx_posts_roomid ON posts (roomId); -- ルームIDでの投稿検索用
