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
    if ($row["extension"] == "jpeg") {
        header("Content-Type: image/jpeg");
        echo ($row["thumbnail"]);
    } elseif ($row["extension"] == "png") {
        header("Content-Type: image/png");
        echo ($row["thumbnail"]);
    }
?>
