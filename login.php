<?php
// セッション開始
session_start();

//データベース接続
require_once("db.php");
$dbh = db_connect();

// エラーメッセージの初期化
$errorMessage = "";

// ログインボタンが押された場合
if (isset($_POST["login"])) {
    
    //入力チェック
    if (empty($_POST["mail"])) {  // emptyは値が空のとき
        $errorMessage = 'メールアドレスが未入力です';
    } else if (empty($_POST["password"])) {
        $errorMessage = 'パスワードが未入力です。';
    }

    //入力されていたら認証する
    if (!empty($_POST["mail"]) && !empty($_POST["password"])) {
        // 入力したメールアドレス、パスワードを格納
        $mail = $_POST["mail"];  
        $password = $_POST["password"];
        //crypt関数でハッシュ化
        $salt = 'abcde12345abcde12345zz';
        $cost = 12;
        $password_hash = crypt( $password, '$2a$' . $cost . '$' . $salt . '$'); 
        //会員情報を取得  
        $result = $dbh->query("SELECT * FROM member");
        foreach($result as $row) {
            //パスワードの照合
            if (($mail==$row['mail'])&&($password_hash == $row['password'])) {
                session_regenerate_id(true);
                $_SESSION["account"] = $row['account'];
                $_SESSION["id"] = $row["id"];
                header("Location:home.php");  // メイン画面へ遷移
                exit();  // 処理終了
            } else {
            // 認証失敗
                $errorMessage = 'メールアドレスあるいはパスワードに誤りがあります。';
            }
        }
    } 
}
?>

<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
<title>Street_ログイン</title>
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
    <div class="form">
        <form name="loginForm" action="" method="POST">
                <h2>ログイン</h2>
                <p><font color="#ff0000"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES); ?></font></p>
                <label for="mail">メールアドレス：</label><input type="text" id="mail" name="mail" placeholder="メールアドレスを入力" value="<?php if (!empty($_POST["mail"])) {echo htmlspecialchars($_POST["mail"], ENT_QUOTES);} ?>">
                <label for="password">パスワード：</label><input type="password" id="password" name="password" value="" placeholder="パスワードを入力">
                <br>
                <input id ="login_button" type="submit" id="login" name="login" value="ログイン">
        </form>
        <br>         
    </div>
    <p><a href="mail_form.php">新規登録はこちらから</a></p>
</div>

</body>
</html>
