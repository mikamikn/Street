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

            // ログイン状態チェック
            if (isset($_SESSION["account"])) {
               $name = $_SESSION["account"];
            } else {
                $name = "ゲスト";
            }
            ?>
            <p><?php echo htmlspecialchars($name, ENT_QUOTES); ?></p>  <!-- ユーザー名をechoで表示 -->

            <?php
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
            <li><a href="search.php">検索</a></li>
            
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
    <ul>
    <?php
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

        //キーワードが入力された場合(投稿者名)
        if (isset($_GET["name_search"])) {
            $name = $_GET["name_search"];
            echo "<h2>検索結果：$name</h2>";

            $sql ="SELECT * FROM media WHERE name LIKE :name LIMIT :offset, :volume";
            $stmt = $dbh -> prepare($sql);
            $stmt -> bindValue(":name", "%$name%", PDO::PARAM_STR);
            $stmt -> bindValue(":offset", $offset, PDO::PARAM_INT);
            $stmt -> bindValue(":volume", $volume, PDO::PARAM_INT);
            $stmt -> execute();

            while ($row = $stmt -> fetch(PDO::FETCH_ASSOC)) {
                $target = $row["fname"];
                $poster = $row["poster"];
                echo ("<li><a href=\"load_media.php?target=$target\">");
                echo ("<img src=\"import_thumbnail.php?target=$target\" alt=\"no image\"><br>");
                echo $row["title"]."<br></a>";

                //投稿者名に投稿者別画面へのリンクを貼付
                $account = $row["name"];
                echo "<a href=\"person.php?poster=$poster&account=$account\">$account<br></a></li>";
            }
        }

    //キーワードが入力された場合(タイトル)
    if (isset($_GET["title_search"])) {
        $title = $_GET["title_search"];
        echo "<h2>検索結果：$title</h2>";
        //キーワードの一部がタイトルに含まれる動画を取得
        $sql = "SELECT * FROM media WHERE title LIKE :title LIMIT :offset, :volume";
        $stmt = $dbh ->prepare($sql);
        $stmt -> bindValue(":title", "%$title%", PDO::PARAM_STR);
        $stmt -> bindValue(":offset", $offset, PDO::PARAM_INT);
        $stmt -> bindValue(":volume", $volume, PDO::PARAM_INT);
        $stmt ->execute();

        while ($row = $stmt ->fetch(PDO::FETCH_ASSOC)) {
            $target = $row["fname"];
            $poster = $row["poster"];
            echo ("<li><a href=\"load_media.php?target=$target\">");
            echo ("<img src=\"import_thumbnail.php?target=$target\" alt=\"no image\"><br>");
            echo $row["title"]."<br></a>";

            //投稿者名に投稿者別画面へのリンクを貼付
            $account = $row["name"];
            echo "<a href=\"person.php?poster=$poster&account=$account\">$account<br></a></li>";
        }

    }

    ?>
    </ul>
    </div>
    <div class="page">
    <ul>
        <?php
            if (isset($_GET["name_search"])) {
                $sql = "SELECT * FROM media WHERE name LIKE :name";
                $st = $dbh -> prepare($sql);
                $st -> bindValue(":name", "%$name%", PDO::PARAM_STR);
                $st -> execute();
                $count = $st -> rowCount();
                $count = ceil($count/$volume);
                for($i=1; $i<=$count; $i++) {
                    echo "<li><a href=\"search.php?name_search=$name&page=$i\">$i</a></li>";
                }
            } elseif (isset($_GET["title_search"])) {
                $sql = "SELECT * FROM media WHERE title LIKE :title";
                $st = $dbh -> prepare($sql);
                $st -> bindValue(":title", "%$title%", PDO::PARAM_STR);
                $st -> execute();
                $count = $st -> rowCount();
                $count = ceil($count/$volume);
                for($i=1; $i<=$count; $i++) {
                    echo "<li><a href=\"search.php?title_search=$title&page=$i\">$i</a></li>";
                }
            }
        ?>
    </ul>
</div>
</body>
</html>