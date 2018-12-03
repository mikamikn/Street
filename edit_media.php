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
            //ログイン状況の確認
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
<?php
	//動画のファイルネームが送られているか確認
   if(isset($_GET["target"]) && $_GET["target"] !== "" && $_GET["id"] == $_SESSION["id"]){
        $target = $_GET["target"];
    } else {
        header("Location: home.php");
    }

    //データベースへ接続
    require_once("db.php");
    $dbh = db_connect();

    if (isset($_FILES["thumbnail"])&&isset($_POST["edit_thumbnail"])) {
    	 //エラーチェック
	    switch ($_FILES['thumbnail']['error']) {
	        case UPLOAD_ERR_OK: // OK
	            break;
	        case UPLOAD_ERR_NO_FILE:   // 未選択
	            throw new RuntimeException('ファイルが選択されていません', 400);
	        case UPLOAD_ERR_INI_SIZE:  // php.ini定義の最大サイズ超過
	            throw new RuntimeException('ファイルサイズが大きすぎます', 400);
	        default:
	            throw new RuntimeException('その他のエラーが発生しました', 500);
	    }


    	$thumbnail = file_get_contents($_FILES["thumbnail"]["tmp_name"]);
		//拡張子の確認
		$tmp = pathinfo($_FILES["thumbnail"]["name"]);
		$extension = $tmp["extension"];
		if ($extension == "jpg" || $extension == "jpeg" || $extension =="JPG" || $extension == "JPEG") {
			$extension = "jpeg";
		} elseif ($extension =="png" || $extension == "PNG") {
			$extension = "png";
		} else {
			echo "非対応ファイルです．<br/>";
	        echo ("<a href=\"edit_media.php\">戻る</a><br/>");
	        exit(1);
		}

		//dbの更新
		$sql = "UPDATE media SET thumbnail = :thumbnail WHERE fname = :target";
		$stmt = $dbh -> prepare($sql);
		$stmt -> bindValue(":thumbnail", $thumbnail, PDO::PARAM_STR);
		$stmt -> bindValue(":target", $target, PDO::PARAM_STR);
		$stmt -> execute();
	}

    if (isset($_POST["title"])&&isset($_POST["edit_title"])) {
    	$title = $_POST["title"];
    	//dbの更新
		$sql = "UPDATE media SET title = :title WHERE fname = :target";
		$stmt = $dbh -> prepare($sql);
		$stmt -> bindValue(":title", $title, PDO::PARAM_STR);
		$stmt -> bindValue(":target", $target, PDO::PARAM_STR);
		$stmt -> execute();
    }

    if (isset($_POST["delete"])) {
    	//dbの更新
    	$sql = "DELETE FROM media WHERE fname = :target";
    	$stmt = $dbh -> prepare($sql);
    	$stmt -> bindValue(":target", $target, PDO::PARAM_STR);
    	$stmt -> execute();
    }

    $sql = "SELECT * FROM media WHERE fname = :target";
	$stmt = $dbh->prepare($sql);
    $stmt -> bindValue(":target", $target, PDO::PARAM_STR);
    $stmt -> execute();
    $row = $stmt -> fetch(PDO::FETCH_ASSOC);

    echo "<h2>現在のサムネイル</h2>";
    echo "<img src=\"import_thumbnail.php?target=$target\" alt=\"no image\"><br>";
    echo "<h2>現在のタイトル</h2>";
    echo "<p>".$row["title"]."</p>";


?>
</div>
	<div class="form">
		<form action="" method="post" enctype="multipart/form-data">
			<h2>編集</h2>
			<label for="thumbnail">新しいサムネイル</label><input type="file" name="thumbnail"><br><br>
			<input type="submit" value="サムネイルの変更" name="edit_thumbnail" id="thumb"><br><br>
			<label for="title">新しいタイトル</label><input type="text" name="title"><br><br>
			<input type="submit" value="タイトルを変更" name="edit_title" id="title">
        </form>
        <form action="" method="post">
			<h2>削除</h2>
			<input type="submit" value="削除" name="delete" id="delete">
		</form>
	</div>
</body>
</html>
