<?php
 
function db_connect(){
	$dsn = 'データベース名';
	$user = 'ユーザー名';
	$password = 'パスワード';
	
	try{
		$dbh = new PDO($dsn, $user, $password);
		return $dbh;
	}catch (PDOException $e){
	    	print('Error:'.$e->getMessage());
	    	die();
	}
}
 
?>