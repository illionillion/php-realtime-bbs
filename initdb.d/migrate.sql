CREATE TABLE posts (
    id SERIAL PRIMARY KEY,       -- idは連番で自動増分
    name VARCHAR(255) NOT NULL,  -- 名前（空白不可）
    comment TEXT NOT NULL,       -- コメント（空白不可）
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- 作成日時（デフォルトで現在時刻）
);
