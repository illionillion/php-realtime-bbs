<?php
require_once("./lib/connect-db.php");

// 投稿データを取得
$stmt = $pdo->query('SELECT * FROM posts ORDER BY createdAt DESC');
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>掲示板</title>
    <?php require_once('./lib/bootstrap.php'); ?>
    <?php require_once("./lib/socket.io.php"); ?>
</head>

<body class="bg-light">
    <div class="container py-5">
        <h1 class="text-center mb-4"><i class="fas fa-comments"></i> 掲示板</h1>

        <!-- 投稿フォーム -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <h2 class="h5 mb-3"><i class="fas fa-pencil-alt"></i> 新規投稿</h2>
                <form action="actions/send.php" method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label w-100">名前</label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="名前を入力" required>
                    </div>
                    <div class="mb-3">
                        <label for="comment" class="form-label w-100">コメント</label>
                        <textarea name="comment" id="comment" class="form-control" placeholder="コメントを入力" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-paper-plane"></i> 投稿する
                    </button>
                </form>
            </div>
        </div>

        <!-- 投稿リスト -->
        <h2 class="h5 mb-3"><i class="fas fa-list"></i> 投稿一覧</h2>
        <ul id="posts" class="list-group mb-4">
            <?php foreach ($posts as $post): ?>
                <li class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong><i class="fas fa-user-circle"></i> <?= htmlspecialchars($post['name']) ?></strong>
                            <pre class="mb-1"><?= htmlspecialchars($post['comment']) ?></pre>
                        </div>
                        <small class="text-muted"><i class="far fa-clock"></i>  <?= date('Y/m/d H:i', strtotime($post['createdat'])) ?></small>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <template id="post-template">
        <li class="list-group-item">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <strong><i class="fas fa-user-circle"></i> <span class="post-name"></span></strong>
                    <pre class="mb-1 post-comment"></pre>
                </div>
                <small class="text-muted"><i class="far fa-clock"></i> <span class="post-createdat"></span></small>
            </div>
        </li>
    </template>

</body>

</html>