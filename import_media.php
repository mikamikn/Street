<?php
    if(isset($_GET["target"]) && $_GET["target"] !== ""){
        $target = $_GET["target"];
    } else {
        header("Location: home.php");
    }
   
    require_once("db.php");
    $dbh = db_connect();

    $sql = "SELECT * FROM media WHERE fname = :target;";
    $stmt = $dbh->prepare($sql);
    $stmt -> bindValue(":target", $target, PDO::PARAM_STR);
    $stmt -> execute();
    $row = $stmt -> fetch(PDO::FETCH_ASSOC);
    header("Content-Type: video/mp4");
    echo ($row["media"]);
?>
