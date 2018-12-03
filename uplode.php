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
    <h2>動画投稿</h2>
    <?php
    session_start();

    // ログイン状態チェック
    if (!isset($_SESSION["account"])) {
        header("Location: logout.php");
        exit;
    }

    //データベースへ接続
    require_once("db.php");
    $dbh = db_connect();

    //ファイルのアップロードがあったとき
    if (isset($_POST["button"])) {
        if (!isset($_FILES["media"]) || !isset($_FILES["thumbnail"]) || !isset($_POST["title"])) {
            echo "動画、サムネイル、タイトルのいずれかが指定されていません。<br/>";
            echo "<a href=\"uplode.php\">戻る</a><br/>";
            exit(1);
        }

    	if (isset($_FILES["media"]["error"])&&isset($_FILES["thumbnail"]["error"])) {
    		 //エラーチェック
    	    switch ($_FILES['media']['error']) {
    	        case UPLOAD_ERR_OK: // OK
    	            break;
    	        case UPLOAD_ERR_NO_FILE:   // 未選択
    	            throw new RuntimeException('ファイルが選択されていません', 400);
    	        case UPLOAD_ERR_INI_SIZE:  // php.ini定義の最大サイズ超過
    	            throw new RuntimeException('ファイルサイズが大きすぎます', 400);
    	        default:
    	            throw new RuntimeException('その他のエラーが発生しました', 500);
    	    }

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
    	}

    	//動画をバイナリーデータにする
    	$media = file_get_contents($_FILES["media"]["tmp_name"]);
    	//mp4かどうかの確認
    	$tmp = pathinfo($_FILES["media"]["name"]);
    	if ($tmp["extension"] != "mp4" && $tmp["extension"] != "MP4") {
            echo "<p>非対応ファイルです．</p>";
            echo ("<p><a href=\"uplode.php\">戻る</a></p>");
            exit(1);
    	}

    	//サムネイルのバイナリーデータ化
    	$thumbnail = file_get_contents($_FILES["thumbnail"]["tmp_name"]);
    	//拡張子の確認
    	$tmp = pathinfo($_FILES["thumbnail"]["name"]);
    	$extension = $tmp["extension"];
    	if ($extension == "jpg" || $extension == "jpeg" || $extension =="JPG" || $extension == "JPEG") {
    		$extension = "jpeg";
    	} elseif ($extension =="png" || $extension == "PNG") {
    		$extension = "png";
    	} else {
    		echo "<p>非対応ファイルです．</p>";
            echo ("<p><a href=\"uplode.php\">戻る</a></p>");
            exit(1);
    	}

    	//DBに格納するファイルネーム設定
        //サーバー側の一時的なファイルネームと取得時刻を結合した文字列にsha256をかける．
        $date = getdate();
        $fname = $_FILES["media"]["tmp_name"].$date["year"].$date["mon"].$date["mday"].$date["hours"].$date["minutes"].$date["seconds"];
        $fname = hash("sha256", $fname);

        //タイトル
        $title = $_POST["title"];

        //投稿者
        $poster = $_SESSION["id"];
        $name = $_SESSION["account"];

        //画像・動画をDBに格納．
        $sql = "INSERT INTO media (fname, media, thumbnail, extension, title, poster, name) 
                        VALUES (:fname, :media, :thumbnail, :extension, :title, :poster, :name)";
        $stmt = $dbh->prepare($sql);
        $stmt -> bindValue(":fname",$fname, PDO::PARAM_STR);
        $stmt -> bindValue(":media",$media, PDO::PARAM_STR);
        $stmt -> bindValue(":thumbnail",$thumbnail, PDO::PARAM_STR);
        $stmt -> bindValue(":extension",$extension, PDO::PARAM_STR);
        $stmt -> bindValue(":title",$title, PDO::PARAM_STR);
        $stmt -> bindValue(":poster",$poster, PDO::PARAM_INT);
        $stmt -> bindValue(":name", $name, PDO::PARAM_STR);
        $stmt -> execute();

        //アップロードが終わるとホーム画面へ
        header("Location: home.php");
        exit(1);
    }
    ?>
</div>
<div class="form">
    <br>
    <form action="uplode.php" enctype="multipart/form-data" method="post">
        <label>動画アップロード</label>
        <input type="file" name="media">
        <br>
        <label>サムネイル</label>
        <input type="file" name="thumbnail">
        <br>
        <label>動画タイトル</label>
        <input type="text" name="title">
        <br>
        <br>
        <input type="submit" value="アップロード" name="button" id="button">
        <p>サムネイルはjpeg,png方式のみ対応しています<br>動画はmp4方式のみ対応しています</p>
    </form>
</div>
</body>
</html>