<?php
session_start();
// セッションにuser_idが存在するかチェック
if (isset($_SESSION['user_id'])) {
    // セッションがない場合はサインインへリダイレクト
    header("Location: /");
    exit;
}
$mode = isset($_GET["mode"]) && $_GET["mode"] === "signup" ? "signup" : "signin";
$title = $mode === "signin" ? "サインイン" : "サインアップ";
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <?php require_once('../lib/bootstrap.php'); ?> <!-- Bootstrapを読み込む -->
</head>

<body>
    <div class="container mt-5">
        <!-- フォーム部分 -->
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="mb-4 text-center"><?= $mode === "signin" ? "サインイン" : "サインアップ" ?></h2>
                <!-- タブ切り替え -->
                <ul class="nav mb-4 nav-pills nav-fill">
                    <li class="nav-item">
                        <a class="nav-link <?= $mode === "signin" ? "active" : "" ?>" href="/auth">サインイン</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $mode === "signup" ? "active" : "" ?>" href="/auth?mode=signup">サインアップ</a>
                    </li>
                </ul>
                <form method="POST" action="<?= $mode === 'signin' ? '/actions/auth-signin.php' : '/actions/auth-signup.php' ?>">
                    <!-- 名前 (サインアップ時のみ表示) -->
                    <?php if ($mode === "signup") : ?>
                        <div class="mb-3">
                            <label for="name" class="form-label w-100">名前</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="山田太郎" required>
                        </div>
                        <div class="mb-3">
                            <label for="displayName" class="form-label w-100">表示名</label>
                            <input type="text" class="form-control" id="displayName" name="displayName" placeholder="Taro Yamada">
                        </div>
                    <?php endif; ?>

                    <!-- メールアドレス -->
                    <div class="mb-3">
                        <label for="email" class="form-label w-100">メールアドレス</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="example@domain.com" required>
                    </div>

                    <!-- パスワード -->
                    <div class="mb-3">
                        <label for="password" class="form-label w-100">パスワード</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="********" required>
                    </div>

                    <?php if (!empty($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger">
                            <strong>エラー:</strong> <?= htmlspecialchars($_SESSION['error_message']) ?>
                        </div>
                        <?php unset($_SESSION['error_message']); ?>
                    <?php endif; ?>

                    <!-- 送信ボタン -->
                    <div class="d-flex justify-content-center">
                        <button type="submit" class="btn btn-dark">
                            <?= $mode === "signin" ? "サインイン" : "サインアップ" ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>