<html>
<head>
<?php
error_reporting(0);
include_once "navbar.php";
include_once "connection.php";
?>
<link rel="stylesheet" type="text/css" href="css/style.css">
<link href="bootstrap/css/bootstrap.css" rel="stylesheet">
</head>
<body> 
<div class="wrap">
<?php 
$mapid = ($_GET['mapid']);
	$stmt = $conn->prepare('SELECT * FROM Maps WHERE mapid = ?');
	$stmt->bind_param('s', $mapid);
	$stmt->execute();
	$results = $stmt->get_result();
    $row = $results->fetch_assoc();
    if(!$row){echo "<center>No map found for the given map id</center>"; return;}
        echo "<title>".$row["name"]."</title>";
        echo "<center><br/><img src='maps/".$row["name"].".png'></center>";
        echo "name: ".$row["name"]."<br/>";




		$stmt = $conn->prepare('SELECT Count(*) As total FROM Playedmaps WHERE mapid = ?');
        $stmt->bind_param('s', $mapid);
        $stmt->execute();
        $results = $stmt->get_result();
        $row2 = $results->fetch_assoc();
        echo "Amount of times played: ".$row2["total"];
        $conn->close();

?>


</div></div>
<?php include_once("footer.php") ?>
</body>

</html>