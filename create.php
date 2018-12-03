<?php
	$dsn = 'mysql:dbname=tt_161_99sv_coco_com;host=localhost';
	$user = 'tt-161.99sv-coco';
	$password = 'Ci9SGuRQ';
	$pdo = new PDO($dsn,$user,$password);
	
/*
	$stmt = $pdo -> query("DROP TABLE media");
	$sql = "CREATE TABLE media(
		id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
		fname TEXT NOT NULL,
		media LONGBLOB NOT NULL,
		thumbnail LONGBLOB NOT NULL,
		extension TEXT NOT NUll,
		title TEXT NOT NULL,
		count INT DEFAULT 0) ENGINE=InnoDB";
*/
	$name = "mkmkng";
	$sql = "update media set name = :name where poster = 4";
	$stmt = $pdo -> prepare($sql);
	$stmt -> bindValue(":name", $name, PDO::PARAM_STR);
	$stmt ->execute();
?>