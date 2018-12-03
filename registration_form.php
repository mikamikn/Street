<?php
session_start();

header("Content-type: text/html; charset=utf-8");

//クロスサイトリクエストフォージェリ（CSRF）対策
$_SESSION['token'] = sha1(uniqid(mt_rand(), true));;
$token = $_SESSION['token'];

//クリックジャッキング対策
header('X-FRAME-OPTIONS: SAMEORIGIN');

//データベース接続
require_once("db.php");
$dbh = db_connect();

//エラーメッセージの初期化
$errors = array();

if(empty($_GET)) {
	header("Location: registration_form.php");
	exit();
}else{
	//GETデータを変数に入れる
	$urltoken = $_GET['urltoken'];
	//メール入力判定
	if ($urltoken == ''){
		$errors['urltoken'] = "もう一度登録をやりなおして下さい。";
	}else{
		try{
			//例外処理を投げる（スロー）ようにする
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			//flagが0の未登録者・仮登録日から24時間以内
			$stmt = $dbh->prepare("SELECT mail FROM pre_member WHERE urltoken=(:urltoken) AND flag =0 AND date > now() - interval 24 hour");
			$stmt->bindValue(':urltoken', $urltoken, PDO::PARAM_STR);
			$stmt->execute();
			
			//レコード件数取得
			$row_count = $stmt->rowCount();
			
			//24時間以内に仮登録され、本登録されていないトークンの場合
			if( $row_count ==1){
				$mail_array = $stmt->fetch();
				$mail = $mail_array[mail];
				$_SESSION['mail'] = $mail;
			}else{
				$errors['urltoken_timeover'] = "このURLはご利用できません。有効期限が過ぎた等の問題があります。もう一度登録をやりなおして下さい。";
			}
			
			//データベース接続切断
			$dbh = null;
			
		}catch (PDOException $e){
			print('Error:'.$e->getMessage());
			die();
		}
	}
}

?>

<!DOCTYPE html>
<html>
<head>
<title>Street_会員登録画面</title>
<meta charset="utf-8">
<link rel="stylesheet" href="registration.css">
</head>
<body>
<h1>Street</h1>
<h2>会員登録</h2>

<?php if (count($errors) === 0): ?>

<form action="registration_check.php" method="post">

<p>メールアドレス：<?=htmlspecialchars($mail, ENT_QUOTES, 'UTF-8')?></p>
<p>アカウント名：<input type="text" name="account"></p>
<p>パスワード：<input type="text" name="password"></p>
 
<input type="hidden" name="token" value="<?=$token?>">
<input id="button" type="submit" value="確認する">
 
</form>
 
<?php elseif(count($errors) > 0): ?>

<?php
foreach($errors as $value){
	echo "<p>".$value."</p>";
}
?>
<p><a href="mail_form.php">新規登録画面へ戻る</a></p>
<?php endif; ?>

</body>
</html>