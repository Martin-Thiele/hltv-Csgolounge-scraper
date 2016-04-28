<html>
<head>
<?php
error_reporting(0);
include_once "navbar.php";
include_once "connection.php";
?>
<link rel="stylesheet" type="text/css" href="css/style.css">
<link href="bootstrap/css/bootstrap.css" rel="stylesheet">
<title>Maps</title>
</head>
<body>
<div class="wrap">
<h1>Maps</h1>
<div class="table-hover" >
<?php
$sql = "SELECT * FROM Maps WHERE mapid !='9' and mapid !='10' ORDER BY mapid";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    echo "<table class='table table-hover'>";
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo "<tr><td width='900px'><a href='mapid.php?mapid=".$row["mapid"]."'>".$row["name"]."</td></tr>";
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