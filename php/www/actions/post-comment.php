<?php
require_once("../lib/connect-db.php");
require_once("../lib/session-check.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roomId = $_POST['roomid'] ?? null;
    $comment = trim($_POST['comment'] ?? '');

    // セッションからユーザーIDを取得
    $userId = $_SESSION['user_id'] ?? null;
    $displayName = $_SESSION['user_displayName'] ?? null; // ユーザー名も取得
    
    if (!$roomId || empty($comment)) {
        $_SESSION['error'] = 'コメントは必須です。';
        header("Location: ../rooms/index.php?id=" . urlencode($roomId));
        exit();
    }

    // データベースに投稿を保存
    $stmt = $pdo->prepare('INSERT INTO posts (roomId, userId, comment) VALUES (:roomId, :userId, :comment) RETURNING roomId, userId, comment, createdAt');
    $stmt->execute(['roomId' => $roomId, 'userId' => $userId, 'comment' => $comment]);
    $postData = $stmt->fetch(PDO::FETCH_ASSOC); // 投稿データを取得

    // Socket.io サーバーに新しい投稿を通知
    $url = "http://express:3000/new_comment";
    $data = json_encode([
        'roomId' => htmlspecialchars($postData['roomid']),
        'name' => htmlspecialchars($displayName),
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
