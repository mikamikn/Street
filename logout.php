<?php
session_start();

if (isset($_SESSION["account"])) {
    $errorMessage = "ログアウトしました。";
} else {
    $errorMessage = "セッションがタイムアウトしました。";
}

// セッションの変数のクリア
$_SESSION = array();

// セッションクリア
@session_destroy();
?>

<!doctype html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>ログアウト</title>
        <link rel="stylesheet" href="home.css">
    </head>
    <body>
      <div class="top">
    <div class="title">
        <h1><a href="home.php">Street</a></h1>
    </div>

    <div class="login">
    </div>
</div>

<div class="menu">
    <ul>
        <li><a href="home.php">ホーム</a></li>
        <li><a href="new_media.php">新着</a></li>
        <li><a href="famous_media.php">人気</a></li>
    </ul>
</div>

<div class="search">
    <form action="search.php" method="get">
        <input type="text" name="name_search" placeholder="投稿者名">
        <input type="submit" name="name_button" value="投稿者名で検索">
    </form>
    <form action="search.php" method="get">
        <input type="text" name="title_search" placeholder="タイトル">
        <input type="submit" name="title_button" value="タイトルで検索">
    </form>
</div>
<div class="contents">
    <?php echo htmlspecialchars($errorMessage, ENT_QUOTES); ?>
    <ul>
        <li><a href="login.php">ログイン画面に戻る</a></li>
        <li><a href="home.php">ホーム画面に戻る</a></li>
    </ul>
</div>
</body>
</html>
