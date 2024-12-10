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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $comment = $_POST['comment'];

    // 新しい投稿をDBに追加
    $stmt = $pdo->prepare('INSERT INTO posts (name, comment) VALUES (:name, :comment)');
    $stmt->execute(['name' => $name, 'comment' => $comment]);

    // Socket.ioサーバーに新しい投稿を通知
    $url = getenv('EXPRESS_URL'.'/new_post');  // なぜか送信されない
    $data = json_encode(['name' => $name, 'comment' => $comment]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    curl_exec($ch);
    curl_close($ch);
}

// 投稿後はindex.phpにリダイレクト
header('Location: index.php');
exit;
