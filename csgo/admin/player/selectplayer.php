<html>
<head>
<?php
include_once "../navbar.php";
include_once "../connection.php";
?>
        <link href="<?php echo "//" . $url . "/css/style.css"; ?>" rel="stylesheet">
        <link href="<?php echo "//" . $url . "/bootstrap/css/bootstrap.css"; ?>" rel="stylesheet">
</head>
<body>
<div class="wrap">
<div>
<h1 style='float:left'>Players</h1>
<h1 style='text-align: right'><a href="selecttransfer.php">Transfers</a></h1>
</div>
<div class="table table-hover" >
<?php
$sql = "SELECT pid, Teams.tid, ign, name, tcountry, T.country FROM Teams
RIGHT JOIN
(SELECT Players.pid, tid, ign, country FROM Players
LEFT JOIN Belongs_to ON Players.pid = Belongs_to.pid  WHERE prim = 1) AS T ON Teams.tid = T.tid
ORDER BY name asc";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    echo "<table class='table table-hover'><thead><tr><td width='450px'><b>Playername</b></td><td width='450px'><b>Team</b></td></tr></thead>";
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $flag = "//" . $url . "/flags/" . str_replace(' ', '', (strtolower($row["country"]))) . ".png";
        $teamflag = "//" . $url . "/flags/" . str_replace(' ', '', (strtolower($row["tcountry"]))) . ".png";
        echo "<tr><td><img src='".$flag."'> <a href='editplayer.php?pid=".$row["pid"]."'>".$row["ign"]."</td></a>
        <td><img src='".$teamflag."'> <a href='editteam.php?pid=".$row["pid"]."'>".$row["name"]."</td></tr>";
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