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
<h1 style='float:left'><a href="selectplayer.php">Players</a></h1>
<h1 style='text-align: right'>Transfers</h1>
</div>
<div class="table table-hover" >
<?php
$sql = "SELECT Players.pid, ign, country, tid1, tid2, namex, name, tcountryx, tcountry, transdate, id FROM Players
INNER JOIN
(SELECT pid, tid1, tid2, namex, name, tcountryx, tcountry, transdate, id FROM Teams
INNER JOIN
(SELECT pid, tid AS tidx, tid1, tid2, name as namex, tcountry as tcountryx, transdate, id FROM Teams
INNER JOIN
(SELECT pid, tid1, tid2, transdate, id FROM Playertransfers) AS T
ON Teams.tid = T.tid1) AS TT
ON Teams.tid = TT.tid2) AS TTT
ON Players.pid = TTT.pid ORDER BY transdate desc, name asc, namex asc";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
        echo "
    <div class='table table-hover'>
    <table class='table table-hover'>
    <thead>
    <tr>
        <td ><b>Date</b></td>
        <td ><b>Playername</b></td>
        <td ><b>Team1</b></td>
        <td ><b></b></td>
        <td ><b>Team2</b></td>
        <td style='text-align: center'><b></b></td>
    </tr></thead>";

    // output data of each row
    while($row = $result->fetch_assoc()) {
        $flag = "//" . $url . "/flags/" . str_replace(' ', '', (strtolower($row["country"]))) . "";
        $teamflag = "//" . $url . "/flags/" . str_replace(' ', '', (strtolower($row["tcountry"]))) ."";
        $teamflag2 = "//" . $url . "/flags/" . str_replace(' ', '', (strtolower($row["tcountryx"]))) ."";
        $newDate = date("d.m.Y", strtotime($row["transdate"]));
        echo "<tr>
        <td><b>".$newDate."</b></td>
        <td><img src='".$flag.".png''> <a href='".$url."/playerid.php?pid=".$row["pid"]."'>".$row["ign"]."</td></a>
        <td><img src='".$teamflag2.".png''> <a href='".$url."/teamid.php?tid=".$row["tid1"]."'>".$row["namex"]."</a></td>
        <td>=></td>
        <td><img src='".$teamflag.".png''> <a href='".$url."/teamid.php?tid=".$row["tid2"]."'>".$row["name"]."</a></td>
        <td style='text-align: center'><a href='edittransfer.php?id=".$row["id"]."'><img src='../edit.png'></a></td>
        </tr>";}
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