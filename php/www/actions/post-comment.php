<?php
require_once("../lib/connect-db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roomId = $_POST['roomid'];
    $name = $_POST['name'];
    $comment = $_POST['comment'];

    // データベースに投稿を保存
    $stmt = $pdo->prepare('INSERT INTO posts (roomId, name, comment) VALUES (:roomId, :name, :comment) RETURNING roomId, name, comment, createdAt');
    $stmt->execute(['roomId' => $roomId, 'name' => $name, 'comment' => $comment]);
    $postData = $stmt->fetch(PDO::FETCH_ASSOC); // 投稿データを取得

    // Socket.io サーバーに新しい投稿を通知
    $url = "http://express:3000/new_comment";
    $data = json_encode([
        'roomId' => htmlspecialchars($postData['roomid']),
        'name' => htmlspecialchars($postData['name']),
        'comment' => htmlspecialchars($postData['comment']),
        'createdAt' => date('Y/m/d H:i', strtotime($postData['createdat']))
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_exec($ch);
    curl_close($ch);
}

// リダイレクト先を設定（ルーム詳細ページに戻る）
header("Location: ../rooms/index.php?id=" . urlencode($roomId));
exit;
