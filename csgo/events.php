<html>
<head>
<?php
error_reporting(0);
include_once "navbar.php";
include_once "connection.php";
?>
<link rel="stylesheet" type="text/css" href="css/style.css">
<link href="bootstrap/css/bootstrap.css" rel="stylesheet">
<title>Events</title>
</head>
<body>
<div class="wrap">
<h1>Events</h1>
<div class="table table-hover" >
<?php
$sql = "SELECT * From Competitions WHERE cid != 71 ORDER BY name asc";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    echo "<table class='table table-hover'>";
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo "<tr><td><a href='compid.php?cid=".$row["cid"]."'>".$row["name"]."</td></a>
        </tr>";
    }
    echo "</table>";
} else {
    echo "0 results";
}
$conn->close();
?>
</div>
</div>
<?php include_once("footer.php") ?>
</body>
</html>