<?php
require_once("../lib/connect-db.php");
require_once("../lib/session-check.php");  // セッションチェックを行う

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // セッションからユーザーIDを取得
    $userId = $_SESSION['user_id'] ?? null; // セッションが無ければnull
    $displayName = $_SESSION['user_displayName'] ?? null; // ユーザー名も取得

    if ($userId === null) {
        // セッションが無ければログインページにリダイレクト
        header('Location: /login.php');
        exit();
    }

    // POSTされたタイトルを取得
    $title = trim($_POST['title'] ?? '');

    // タイトルが空であればエラーメッセージを設定してリダイレクト
    if (empty($title)) {
        $_SESSION['error'] = 'タイトルは必須です。';
        header('Location: /index.php');
        exit();
    }

    // ルームIDを生成
    $roomId = uniqid();

    // ルームをデータベースに挿入
    $stmt = $pdo->prepare('
        INSERT INTO rooms (roomId, title, userId) 
        VALUES (:roomId, :title, :userId) 
        RETURNING roomId, title, createdAt
    ');

    // データベースに挿入
    $stmt->execute([
        ':roomId' => $roomId,
        ':title'  => $title,
        ':userId' => $userId
    ]);

    // 挿入したルームの情報を取得
    $roomData = $stmt->fetch(PDO::FETCH_ASSOC);

    // 新しいルームの情報をSocket.ioサーバーに送信
    $url = "http://express:3000/new_room";
    $data = json_encode([
        'roomId'   => htmlspecialchars($roomData['roomid']),
        'title'    => htmlspecialchars($roomData['title']),
        'createdBy' => $displayName,  // 作成者のIDを送信（必要に応じて名前を取得して送る）
        'createdAt' => date('Y/m/d H:i', strtotime($roomData['createdat']))
    ]);

    // cURLでSocket.ioサーバーにデータを送信
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_exec($ch);
    curl_close($ch);

    // トップページにリダイレクト
    header('Location: ../index.php');
    exit();
}
