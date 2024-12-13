<?php
require_once("./lib/connect-db.php");

// ルームのデータを取得
$stmt = $pdo->query('SELECT * FROM rooms ORDER BY createdAt DESC');
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>掲示板一覧</title>
    <?php require_once('./lib/bootstrap.php'); ?>
    <?php require_once("./lib/socket.io.php"); ?>
</head>

<body class="bg-light">
    <div class="container py-5">
        <h1 class="text-center mb-4"><i class="fas fa-comments"></i> 掲示板一覧</h1>

        <!-- ルーム作成フォーム -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <h2 class="h5 mb-3"><i class="fas fa-pencil-alt"></i> スレッド作成</h2>
                <form action="actions/create-room.php" method="POST">
                    <div class="mb-3">
                        <label for="title" class="form-label w-100">スレッドのタイトル</label>
                        <input type="text" name="title" id="title" class="form-control" placeholder="スレッドのタイトルを入力" required>
                    </div>
                    <div class="mb-3">
                        <label for="createdBy" class="form-label w-100">作成者名</label>
                        <input type="text" name="createdBy" id="createdBy" class="form-control" placeholder="作成者の名前を入力" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-paper-plane"></i> 作成する
                    </button>
                </form>
            </div>
        </div>

        <!-- ルーム一覧 -->
        <h2 class="h5 mb-3"><i class="fas fa-list"></i> スレッド一覧</h2>
        <ul id="rooms" class="list-group mb-4">
            <?php foreach ($rooms as $room): ?>
                <li class="list-group-item">
                    <a href="/rooms?id=<?= htmlspecialchars($room['roomid']) ?>" class="room-link text-decoration-none">
                        <strong class="title"><?= htmlspecialchars($room['title']) ?></strong>
                        <small class="desc text-muted">（作成者: <?= htmlspecialchars($room['createdby']) ?>, <?= date('Y/m/d H:i', strtotime($room['createdat'])) ?>）</small>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Socket.ioで後から追加する用 -->
    <template id="room-template">
        <li class="list-group-item">
            <a href="" class="room-link text-decoration-none">
                <strong class="title"></strong>
                <small class="desc text-muted"></small>
            </a>
        </li>
    </template>
</body>

</html>