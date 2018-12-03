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
	header("Location: registration_mail_form.php");
	exit();
}

$mail = $_SESSION['mail'];
$account = $_SESSION['account'];


//パスワードのハッシュ化
$salt = 'abcde12345abcde12345zz';
$cost = 12;
$password_hash = crypt( $_SESSION['password'], '$2a$' . $cost . '$' . $salt . '$');

//ここでデータベースに登録する
try{
	//例外処理を投げる（スロー）ようにする
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	//トランザクション開始
	$dbh->beginTransaction();
	
	//memberテーブルに本登録する
	$stmt = $dbh->prepare("INSERT INTO member (account,mail,password) VALUES (:account,:mail,:password_hash)");
	//プレースホルダへ実際の値を設定する
	$stmt->bindValue(':account', $account, PDO::PARAM_STR);
	$stmt->bindValue(':mail', $mail, PDO::PARAM_STR);
	$stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
	$stmt->execute();
		
	//pre_memberのflagを1にする
	$stmt = $dbh->prepare("UPDATE pre_member SET flag=1 WHERE mail=(:mail)");
	//プレースホルダへ実際の値を設定する
	$stmt->bindValue(':mail', $mail, PDO::PARAM_STR);
	$stmt->execute();
	
	// トランザクション完了（コミット）
	$dbh->commit();
		
	//データベース接続切断
	$dbh = null;
	
	//セッション変数を全て解除
	$_SESSION = array();
	
	//セッションクッキーの削除・sessionidとの関係を探れ。つまりはじめのsesssionidを名前でやる
	if (isset($_COOKIE["PHPSESSID"])) {
    		setcookie("PHPSESSID", '', time() - 1800, '/');
	}
	
 	//セッションを破棄する
 	session_destroy();
 	
 	//登録完了のメールを送信
 	//メールの宛先
	$mailTo = $mail;
 
	//Return-Pathに指定するメールアドレス
	$returnMail = '123456789@example.com';
 
	$name = "Street";
	$mail = '123456789@example.com';
	$subject = "【Street】会員登録完了のお知らせ";

$body = <<< EOM
Streetへの登録が完了しました。ご登録ありがとうございます。
ログインはこちらから→http://tt-161.99sv-coco.com/mission6/login.php
EOM;

	mb_language('ja');
	mb_internal_encoding('UTF-8');
 
	//Fromヘッダーを作成
	$header = 'From: ' . mb_encode_mimeheader($name). ' <' . $mail. '>';
 
	mb_send_mail($mailTo, $subject, $body, $header, '-f'. $returnMail);


	
} catch (PDOException $e){
	//トランザクション取り消し（ロールバック）
	$dbh->rollBack();
	$errors['error'] = "もう一度やりなおして下さい。";
	print('Error:'.$e->getMessage());
}

?>

<!DOCTYPE html>
<html>
<head>
<title>Street_会員登録完了画面</title>
<meta charset="utf-8">
<link rel="stylesheet" href="registration.css">
</head>
<body>
 
<?php if (count($errors) === 0): ?>
<h1>Street</h1>
<h2>会員登録完了</h2>

<p>登録完了いたしました。ログイン画面からどうぞ。</p>
<p><a href="login.php">ログイン画面</a></p>

<?php elseif(count($errors) > 0): ?>

<?php
foreach($errors as $value){
	echo "<p>".$value."</p>";
}
?>

<?php endif; ?>
 
</body>
</html>