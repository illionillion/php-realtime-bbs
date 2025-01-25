<?php
require_once("../lib/connect-db.php");
require_once("../lib/session-check.php");

// URLからルームIDを取得
$roomId = $_GET['id'] ?? null;

if (!$roomId) {
    echo 'ルームIDが指定されていません。';
    exit;
}

// ルームのデータを取得 (作成者のdisplayNameも一緒に取得)
$stmt = $pdo->prepare('
    SELECT rooms.*, users.displayname as createdby
    FROM rooms 
    LEFT JOIN users ON rooms.userid = users.id
    WHERE roomid = :roomid
');
$stmt->execute([':roomid' => $roomId]);
$room = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$room) {
    echo 'ルームが見つかりませんでした。';
    exit;
}

// コメントを取得し、投稿者のdisplayNameを取得
$stmt = $pdo->prepare('
    SELECT posts.*, users.displayname as commenter_name
    FROM posts
    LEFT JOIN users ON posts.userid = users.id
    WHERE roomid = :roomid
    ORDER BY createdAt DESC
');
$stmt->execute([':roomid' => $roomId]);
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
        <div class="d-flex w-100 justify-content-end">
            <!-- 縦並び -->
            <div class="d-flex flex-column">
                <!-- ユーザー情報表示 -->
                <?php if (isset($_SESSION['user_displayName'])): ?>
                    <p class="text-muted">サインイン中: <?= htmlspecialchars($_SESSION['user_displayName']) ?></p>
                <?php endif; ?>
                <a href="/actions/auth-signout.php" class="btn btn-dark">サインアウト</a>
            </div>
        </div>
        <h1 class="text-center mb-4"><i class="fas fa-comments"></i> <?= htmlspecialchars($room['title']) ?></h1>

        <div>
            <a href="/" class="link">戻る</a>
        </div>

        <p><strong>作成者:</strong> <?= htmlspecialchars($room['createdby']) ?></p>
        <p><strong>作成日時:</strong> <?= date('Y/m/d H:i', strtotime($room['createdat'])) ?></p>

        <!-- コメント機能などを追加 -->
        <h2 class="h5"><i class="fas fa-comment"></i> コメント</h2>
        <!-- コメント一覧表示 -->
        <ul id="comments" class="list-group mb-4">
            <?php foreach ($comments as $comment): ?>
                <li class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong><i class="fas fa-user-circle"></i> <?= htmlspecialchars($comment['commenter_name']) ?></strong>
                            <pre class="mb-1"><?= htmlspecialchars($comment['comment']) ?></pre>
                        </div>
                        <small class="text-muted"><i class="far fa-clock"></i> <?= date('Y/m/d H:i', strtotime($comment['createdat'])) ?></small>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
        <!-- コメント投稿フォーム -->
        <form action="/actions/post-comment.php" method="POST">
            <input type="hidden" name="roomid" value="<?= htmlspecialchars($roomId) ?>">
            <div class="mb-3">
                <label for="comment" class="form-label w-100">コメント</label>
                <textarea name="comment" id="comment" class="form-control" rows="3" placeholder="コメントを入力してください" required></textarea>
            </div>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']) ?></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane"></i> コメントを送信
            </button>
        </form>
    </div>
    <!-- 投稿のテンプレート -->
    <template id="comment-template">
        <li class="list-group-item">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <strong><i class="fas fa-user-circle"></i> <span class="comment-name"></span></strong>
                    <pre class="mb-1 comment-comment"></pre>
                </div>
                <small class="text-muted"><i class="far fa-clock"></i> <span class="comment-createdat"></span></small>
            </div>
        </li>
    </template>

</body>

</html>