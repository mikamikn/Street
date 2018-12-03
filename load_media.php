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
	<?php
		//動画のファイルネームが送られているか確認
	 	if(isset($_GET["target"]) && $_GET["target"] !== ""){
	        $target = $_GET["target"];
	    } else {
	        header("Location: home.php");
	    }
	   
	   	//データベースへ接続
	    require_once("db.php");
	    $dbh = db_connect();

	    //動画の取得
	    $sql = "SELECT * FROM media WHERE fname = :target";
		$stmt = $dbh->prepare($sql);
	    $stmt -> bindValue(":target", $target, PDO::PARAM_STR);
	    $stmt -> execute();
	    $row = $stmt -> fetch(PDO::FETCH_ASSOC);
	    //再生回数
	    $count = $row["count"] + 1;
	    $sql = "UPDATE media SET count = $count WHERE fname = :target";
	    $stmt = $dbh->prepare($sql);
	    $stmt -> bindValue(":target", $target, PDO::PARAM_STR);
	    $stmt -> execute();

	    $title = $row["title"];
	    $count = $row["count"];
        $poster = $row["poster"];
        $account = $row["name"];

	 	//動画の表示
	    echo ("<video src=\"import_media.php?target=$target\" width=\"1024\" height=\"576\" controls></video>");
	    echo "<h2>$title</h2>";
        echo "<p><a href=\"person.php?poster=$poster&account=$account\">$account</a></p>";
	    echo "<p>再生回数:".$count."回</p>";
	?>
</div>
</body>
</html>