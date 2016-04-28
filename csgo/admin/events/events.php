<html>
<head>
<?php
include_once "../navbar.php";
include_once "../connection.php";
?>
        <link href="<?php echo "//" . $url . "/css/style.css"; ?>" rel="stylesheet">
        <link href="<?php echo "//" . $url . "/bootstrap/css/bootstrap.css"; ?>" rel="stylesheet">
<title>Events</title>
</head>
<body>
<div class="wrap">
<h1>Events</h1>
<div class="table table-hover" >
<?php
$sql = "SELECT * From Competitions";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    echo "<table class='table table-hover'>
    <thead><tr>
    <td><b>Name</b></td>
    <td><b>Edit</b></td>
    </tr>
    </thead>";
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo "<tr><td><a href='compid.php?cid=".$row["cid"]."'>".$row["name"]."</a></td>
                  <td><a href='editcomp.php?cid=".$row["cid"]."'>Edit</a></td>
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
</body>
</html>