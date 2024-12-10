<?php
require("../lib/connect-db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $comment = $_POST['comment'];

    // 新しい投稿をDBに追加し、createdAtを取得
    $stmt = $pdo->prepare('
        INSERT INTO posts (name, comment) 
        VALUES (:name, :comment) 
        RETURNING createdAt
    ');
    $stmt->execute(['name' => $name, 'comment' => $comment]);

    // 追加した投稿の作成日時（createdAt）を取得
    $createdAt = $stmt->fetchColumn();  // RETURNING句で返されたcreatedAtを取得

    // Socket.ioサーバーに新しい投稿を通知
    $url = "http://express:3000/new_post";
    $data = json_encode([
        'name' => $name, 
        'comment' => $comment, 
        'createdat' => $createdAt // 投稿時刻をそのまま送信
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_exec($ch);
    curl_close($ch);
}

// 投稿後はindex.phpにリダイレクト
header('Location: ../index.php');
exit;
