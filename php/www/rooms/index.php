<?php
require_once("../lib/connect-db.php");

// URLからルームIDを取得
$roomId = $_GET['id'] ?? null;

if (!$roomId) {
    echo 'ルームIDが指定されていません。';
    exit;
}

// ルームのデータを取得
$stmt = $pdo->prepare('SELECT * FROM rooms WHERE roomid = :roomid');
$stmt->execute([':roomid' => $roomId]);
$room = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$room) {
    echo 'ルームが見つかりませんでした。';
    exit;
}

$stmt = $pdo->query('SELECT * FROM posts ORDER BY createdAt DESC');
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($room['title']) ?> - ルーム詳細</title>
    <?php require_once('../lib/bootstrap.php'); ?>
    <?php require_once("../lib/socket.io-comments.php"); ?>
</head>

<body class="bg-light">
    <div class="container py-5">
        <h1 class="text-center mb-4"><i class="fas fa-comments"></i> <?= htmlspecialchars($room['title']) ?></h1>

        <p><strong>作成者:</strong> <?= htmlspecialchars($room['createdby']) ?></p>
        <p><strong>作成日時:</strong> <?= date('Y/m/d H:i', strtotime($room['createdat'])) ?></p>


        <!-- ここにコメント機能などを追加 -->
        <h2 class="h5"><i class="fas fa-comment"></i> コメント</h2>
        <!-- コメント一覧表示 -->
        <ul id="comments" class="list-group mb-4">
            <!-- コメント一覧をここに表示 -->
            <?php foreach ($comments as $comment): ?>
                <li class="list-group-item">
                <li class="list-group-item">
                    <strong class="comment-name"><?= htmlspecialchars($comment["name"]) ?></strong>:
                    <span class="comment-comment"><?= htmlspecialchars($comment["comment"]) ?></span>
                    <small class="comment-date text-muted"><?= date('Y/m/d H:i', strtotime($comment['createdat'])) ?></small>
                </li>
                </li>
            <?php endforeach; ?>
        </ul>
        <!-- コメント投稿フォーム -->
        <form action="/actions/post-comment.php" method="POST">
            <input type="hidden" name="roomid" value="<?= htmlspecialchars($roomId) ?>">
            <div class="mb-3">
                <label for="name" class="form-label w-100">名前</label>
                <input type="text" name="name" id="name" class="form-control" placeholder="あなたの名前を入力してください" required>
            </div>
            <div class="mb-3">
                <label for="comment" class="form-label w-100">コメント</label>
                <textarea name="comment" id="comment" class="form-control" rows="3" placeholder="コメントを入力してください" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane"></i> コメントを送信
            </button>
        </form>
    </div>
    <!-- 投稿のテンプレート -->
    <template id="comment-template">
        <li class="list-group-item">
            <strong class="comment-name"></strong>:
            <span class="comment-comment"></span>
            <small class="comment-date text-muted"></small>
        </li>
    </template>

</body>

</html>