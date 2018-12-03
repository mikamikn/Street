<?php
session_start();
 
header("Content-type: text/html; charset=utf-8");
 
//クロスサイトリクエストフォージェリ（CSRF）対策
$_SESSION['token'] = sha1(uniqid(mt_rand(), true));
$token = $_SESSION['token'];
 
//クリックジャッキング対策
header('X-FRAME-OPTIONS: SAMEORIGIN');
 
?>
 
<!DOCTYPE html>
<html>
<head>
<title>Street_メール登録画面</title>
<meta charset="utf-8">
<link rel="stylesheet" href="registration.css">
</head>
<body>
<h1>Street</h1>
<h2>Streetへようこそ</h2>
<p>以下のフォームにメースアドレスを入力してください</p>
 
<form action="mail_check.php" method="post">
<p>メールアドレス：<input type="text" name="mail" size="50"></p> 
<input type="hidden" name="token" value="<?=$token?>">
<input id="button" type="submit" value="登録">
</form>

<p><a href="home.php">Streetのサイトへ戻る</a></p>
 
</body>
</html>