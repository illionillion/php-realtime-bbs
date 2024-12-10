<?php
// .env ファイルから環境変数を読み込む
$databaseUrl = getenv('DATABASE_URL');  // DATABASE_URLを取得

// DATABASE_URLから接続情報をパース
$parsedUrl = parse_url($databaseUrl);

$host = $parsedUrl['host'];
$db = ltrim($parsedUrl['path'], '/');
$user = $parsedUrl['user'];
$pass = $parsedUrl['pass'];

// PostgreSQLへの接続
$dsn = "pgsql:host=$host;dbname=$db";
try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "接続失敗: " . $e->getMessage();
    exit;
}

// 投稿データを取得
$stmt = $pdo->query('SELECT * FROM posts ORDER BY createdAt DESC');
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>掲示板</title>
    <script src="https://cdn.socket.io/4.0.1/socket.io.min.js"></script>
    <script>
        // Socket.io接続
        const socket = io('<?= getenv('EXPRESS_URL'); ?>'); // ExpressサーバーのURL

        socket.on('new_post', (data) => {
            const postList = document.getElementById('posts');
            const newPost = document.createElement('li');
            newPost.textContent = `${data.name}: ${data.comment}`;
            postList.prepend(newPost);  // 新しい投稿を先頭に追加
        });
    </script>
</head>
<body>
    <h1>掲示板</h1>
    <ul id="posts">
        <?php foreach ($posts as $post): ?>
            <li><?= htmlspecialchars($post['name']) ?>: <?= htmlspecialchars($post['comment']) ?></li>
        <?php endforeach; ?>
    </ul>
    <form action="send.php" method="POST">
        <input type="text" name="name" placeholder="名前" required><br>
        <textarea name="comment" placeholder="コメント" required></textarea><br>
        <button type="submit">投稿</button>
    </form>
</body>
</html>
