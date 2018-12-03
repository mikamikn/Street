<?php
session_start();

header("Content-type: text/html; charset=utf-8");

//クロスサイトリクエストフォージェリ（CSRF）対策のトークン判定
if ($_POST['token'] != $_SESSION['token']){
	echo "不正アクセスの可能性あり";
	exit();
}

//クリックジャッキング対策
header('X-FRAME-OPTIONS: SAMEORIGIN');

//データベース接続
require_once("db.php");
$dbh = db_connect();

//エラーメッセージの初期化
$errors = array();

if(empty($_POST)) {
	header("Location: mail_form.php");
	exit();
}else{
	
	$mail = $_POST['mail'];
	//メール入力判定
	if ($mail == ''){
		$errors['mail'] = "メールが入力されていません。";
	}else{
		if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $mail)){
			$errors['mail_check'] = "メールアドレスの形式が正しくありません。";
		}
		
		$result = $dbh->query("SELECT * FROM member");
		$flag = 0;
		foreach ($result as $row) {
			if($row['mail']==$mail) {
				$flag = 1;
			}
		}
		if($flag == 1) {
			$errors['mail_exist'] ="このメールはすでに利用されています。";
		}
	}
}

//エラーがなければ
if (count($errors) === 0){
	
	$urltoken = hash('sha256',uniqid(rand(),1));
	$url = "http://tt-161.99sv-coco.com/mission6/registration_form.php"."?urltoken=".$urltoken;
	
	//ここでデータベースに登録する
	try{
		//例外処理を投げる（スロー）ようにする
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$stmt = $dbh->prepare("INSERT INTO pre_member (urltoken,mail,date) VALUES (:urltoken,:mail,now() )");
		
		//プレースホルダへ実際の値を設定する
		$stmt->bindValue(':urltoken', $urltoken, PDO::PARAM_STR);
		$stmt->bindValue(':mail', $mail, PDO::PARAM_STR);
		$stmt->execute();
			
		//データベース接続切断
		$dbh = null;	
		
	}catch (PDOException $e){
		print('Error:'.$e->getMessage());
		die();
	}
	
	//メールの宛先
	$mailTo = $mail;
 
	//Return-Pathに指定するメールアドレス
	$returnMail = '123456789@example.com';
 
	$name = "Street";
	$mail = '123456789@example.com';
	$subject = "【Street】会員登録用URLのお知らせ";

$body = <<< EOM
24時間以内に下記のURLからご登録下さい。
{$url}
EOM;

	mb_language('ja');
	mb_internal_encoding('UTF-8');
 
	//Fromヘッダーを作成
	$header = 'From: ' . mb_encode_mimeheader($name). ' <' . $mail. '>';
 
	if (mb_send_mail($mailTo, $subject, $body, $header, '-f'. $returnMail)) {
	
	 	//セッション変数を全て解除
		$_SESSION = array();
	
		//クッキーの削除
		if (isset($_COOKIE["PHPSESSID"])) {
			setcookie("PHPSESSID", '', time() - 1800, '/');
		}
	
 		//セッションを破棄する
 		session_destroy();
 	
 		$message = "メールをお送りしました。24時間以内にメールに記載されたURLからご登録下さい。";
 	
	 } else {
		$errors['mail_error'] = "メールの送信に失敗しました。";
	}	
}

?>

<!DOCTYPE html>
<html>
<head>
<title>メール確認画面</title>
<link rel="stylesheet" href="registration.css">
<meta charset="utf-8">
</head>
<body>
<h1>Street</h1>
<h2>メール確認</h2>

<?php if (count($errors) === 0): ?>

<p><?=$message?></p>
<p><a href="mail_form.php">メール登録画面に戻る</a></p>
<p><a href="home.php">ホーム画面に戻る</a></p>

<!--<p>↓このURLが記載されたメールが届きます。</p>-->
<!--<a href="<?=$url?>"><?=$url?></a>-->

<?php elseif(count($errors) > 0): ?>

<?php
foreach($errors as $value){
	echo "<p>".$value."</p>";
}
?>

<?php endif; ?>
 
</body>
</html>