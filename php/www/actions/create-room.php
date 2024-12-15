<?php
require_once("../lib/connect-db.php");
require_once("../lib/sessioin-check.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $createdBy = $_POST['createdBy'];

    $roomId = uniqid();

    $stmt = $pdo->prepare('INSERT INTO rooms (roomId, title, createdBy) VALUES (:roomId, :title, :createdBy) RETURNING roomId, title, createdBy, createdAt');
    $stmt->execute(['roomId' => $roomId, 'title' => $title, 'createdBy' => $createdBy]);
    // 追加した投稿の情報（name, comment, createdAt）を取得
    $postData = $stmt->fetch(PDO::FETCH_ASSOC);  // 連想配列として取得

    // Socket.ioサーバーに新しい投稿を通知
    $url = "http://express:3000/new_room";
    $data = json_encode([
        'roomId' => htmlspecialchars($postData['roomid']), 
        'title' => htmlspecialchars($postData['title']), 
        'createdBy' => htmlspecialchars($postData['createdby']), 
        'createdAt' => date('Y/m/d H:i', strtotime($postData['createdat'])) // 投稿時刻をそのまま送信
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_exec($ch);
    curl_close($ch);

}
header('Location: ../index.php');
?>
