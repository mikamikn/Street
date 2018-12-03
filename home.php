<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Big Babies</title>
    <link rel="stylesheet" href="home.css">
</head>
<body>
<div class="top">
    <div class="title">
        <h1><a href="home.php">Big Babies</a></h1>
    </div>

    <div class="login">
        <?php
        session_start();
        // ログイン状態チェック
        if (isset($_SESSION["account"])) {
           $name = $_SESSION["account"];
        } else {
            $name = "ゲスト";
        }
        echo "<p>".htmlspecialchars($name, ENT_QUOTES)."</p>";
        if (isset($_SESSION["account"])) {
            echo "<p><a href=\"logout.php\">ログアウト</a></p>";
        } else {
            echo "<p><a href=\"login.php\">ログイン</a></p>";
        }
        ?>
    </div>
</div>

<div class="menu">
    <ul>
        <li><a href="home.php">ホーム</a></li>
        <li><a href="new_media.php">新着</a></li>
        <li><a href="famous_media.php">人気</a></li>
        
        <?php
        if (isset($_SESSION["account"])) {
            $target = $_SESSION["id"];
            echo "<li><a href=\"uplode.php\">投稿</a></li>";
            echo "<li><a href=\"edit.php?target=$target\">編集</a></li>";
        }
        ?>
    </ul>
</div>

<div class="search">
    <form action="search.php" method="get">
        <input type="text" name="name_search" placeholder="投稿者名">
        <input id="name_button" type="submit" name="name_button" value="投稿者名で検索">
    </form>
    <form action="search.php" method="get">
        <input type="text" name="title_search" placeholder="タイトル">
        <input id="title_button" type="submit" name="title_button" value="タイトルで検索">
    </form>
</div>

<div class="contents">
    <h2><a href="new_media.php">新着</a></h2>
    <ul>
        <?php
        //データベースへ接続
        require_once("db.php");
        $dbh = db_connect();

        //動画の取得
        $sql = "SELECT * FROM media ORDER BY id DESC";
        $stmt = $dbh -> query($sql);

        //5個の動画を取得
        $i = 0;
        while ($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
            if ($i >= 8) {
                break;
            }

            //サムネ・タイトルに動画再生画面へのリンクを貼付
            $target = $row["fname"];
            $poster = $row["poster"];
            echo ("<li><a href=\"load_media.php?target=$target\">");
            echo ("<img src=\"import_thumbnail.php?target=$target\" alt=\"no image\"><br>");
            echo $row["title"]."<br></a>";

            //投稿者名に投稿者別画面へのリンクを貼付
            $account = $row["name"];
            echo "<a href=\"person.php?poster=$poster&account=$account\">$account<br></a></li>";

            $i++;
        }
        ?>
    </ul>

    <hr>
    <h2><a href="famous_media.php">人気</a></h2>
    <ul>
        <?php
        //動画の取得
        $sql = "SELECT * FROM media ORDER BY count DESC";
        $stmt = $dbh -> query($sql);

        //5個の動画を取得
        $i = 0;
        while ($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
            if ($i >= 8) {
                break;
            }
            
            //サムネ・タイトルに動画再生画面へのリンクを貼付
            $target = $row["fname"];
            $poster = $row["poster"];
            echo ("<li><a href=\"load_media.php?target=$target\">");
            echo ("<img src=\"import_thumbnail.php?target=$target\" alt=\"no image\"><br>");
            echo $row["title"]."<br></a>";

            //投稿者名に投稿者別画面へのリンクを貼付
            $account = $row["name"];
            echo "<a href=\"person.php?poster=$poster&account=$account\">$account<br></a></li>";

            $i++;
        }
        ?>
    </ul>
</div>
</body>
</html>