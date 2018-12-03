<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Street</title>
    <link rel="stylesheet" href="home.css">
</head>
<body>
    <div class="top">
        <div class="title">
            <h1><a href="home.php">Street</a></h1>
        </div>

        <div class="login">
            <?php
            session_start();
            if (!isset($_SESSION["account"])) {
                header("Location: logout.php");
                exit;
            }
            $name = $_SESSION["account"];
            echo "<p>".htmlspecialchars($name, ENT_QUOTES)."</p>";
            echo "<p><a href=\"logout.php\">ログアウト</a></p>";
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

<div class="contents">
    <h2>編集</h2>
    <p>編集する動画を選択</p>
    <ul>
    <?php
        //投稿者のidが入っているか確認
        if(isset($_GET["target"]) && $_GET["target"] !== "" && $_GET["target"] == $_SESSION["id"]){
            $poster = $_GET["target"];
        } else {
            header("Location: home.php");
        }

        //データベースへ接続
        require_once("db.php");
        $dbh = db_connect();

        //取得する動画の個数
        $volume = 8;
        //offset
        if (isset($_GET["page"])) {
            $page = $_GET["page"];
        } else {
            $page = 1;
        }
        $offset = ($page-1) * $volume;

        //サムネ・タイトルに編集画面へのリンク
        $sql = "SELECT * FROM media WHERE poster = :poster ORDER BY id DESC LIMIT :offset, :volume";
        $stmt = $dbh -> prepare($sql);
        $stmt -> bindValue(":poster", $poster, PDO::PARAM_INT);
        $stmt -> bindValue(":offset", $offset, PDO::PARAM_INT);
        $stmt -> bindValue(":volume", $volume, PDO::PARAM_INT);
        $stmt -> execute();
        while ($row = $stmt -> fetch(PDO::FETCH_ASSOC)) {
             $target = $row["fname"];
             $id = $_SESSION["id"];
            echo ("<li><a href=\"edit_media.php?target=$target&id=$id\">");
            echo ("<img src=\"import_thumbnail.php?target=$target\" alt=\"no image\"><br>");
            echo $row["title"]."<br></a></li>";
        }
    ?>
    </ul>
</div>
<div class="page">
    <ul>
        <?php
            $sql = "SELECT * FROM media WHERE poster = :poster ORDER BY id DESC";
            $stmt = $dbh -> prepare($sql);
            $stmt -> bindValue(":poster", $poster, PDO::PARAM_INT);
            $stmt -> execute();
            $count = $stmt ->rowCount();
            $count = ceil($count / $volume);
            for ($i=1; $i<=$count; $i++) {
                echo "<li><a href=\"edit.php?target=$poster&page=$i\">$i</a></li>";
            }

        ?>
    </ul>
</div>
</body>
</html>