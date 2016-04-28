<html>
<head>
<?php
include_once "../navbar.php";
include_once "../connection.php";
?>
        <link href="<?php echo "//" . $url . "/css/insert.css"; ?>" rel="stylesheet">
        <link href="<?php echo "//" . $url . "/css/style.css"; ?>" rel="stylesheet">
        <link href="<?php echo "//" . $url . "/bootstrap/css/bootstrap.css"; ?>" rel="stylesheet">
        <title>Select team to edit</title>
</head>
<body>
<div class="wrap">
<h1>Select team to edit</h1>
<div class="table table-hover" >
<?php
$sql = "SELECT tid, name, tcountry, logo, active FROM Teams WHERE tid!=40 AND tid !=108 ORDER BY name asc";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    echo "<table class='table table-hover'>";
    // output data of each row
    while($row = $result->fetch_assoc()) {
    	if($row['active'] == 1){
        $teamflag = "//" . $url . "/flags/" . str_replace(' ', '', (strtolower($row["tcountry"]))) . ".png";
        echo "<tr><td width='900px'><img src='".$teamflag."'> <a href='editteam.php?tid=".$row["tid"]."'>".$row["name"]."</td></tr>";
    }
    	else{
    		 $teamflag = "//" . $url . "/flags/" . str_replace(' ', '', (strtolower($row["tcountry"]))) . ".png";
        echo "<tr><td width='900px'><img src='".$teamflag."'> <a href='editteam.php?tid=".$row["tid"]."'>".$row["name"]."</a> (inactive)</td></tr>";
    	}
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