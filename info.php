<?php
//データベースへの接続
$dsn = 'mysql:dbname=tt_161_99sv_coco_com;host=localhost';
$user = 'tt-161.99sv-coco';
$password = 'Ci9SGuRQ';
$pdo = new PDO($dsn,$user,$password);

if ($_POST['delete']) {
	$stmt = $pdo->query('delete from media'); 
}

$result = $pdo->query("SHOW TABLES");
foreach ($result as $row) {
	echo $row[0];
}

$result = $pdo->query('SHOW CREATE TABLE member');
foreach ($result as $row) {
	print_r($row);
}

$result = $pdo->query('SHOW CREATE TABLE media');
foreach ($result as $row) {
	print_r($row);
}
echo "<hr>";

$result = $pdo->query("SELECT * FROM media");
foreach ($result as $row) {
	echo $row["id"].",";
	echo $row["title"].",";
	echo $row["count"].",";
	echo $row["poster"]."<br>";
}



?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
</head>
<body>
	<form action="" method="post">
		<input type="submit" name="delete" value="削除">
	</form>
</body>
</html>