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
<div class="wrap" style="height: 300px">
    <div class="wrapleft">
    <?php
        $pid = ($_GET['pid']);
        $stmt = $conn->prepare('SELECT name, Teams.tid, tcountry, firstname, lastname, ign, image, country, twitter, facebook, steam From Teams 
        INNER JOIN 
        (SELECT T.pid, tid, firstname, lastname, ign, image, country, facebook, twitter, steam From Belongs_to 
            INNER JOIN 
            (SELECT pid, firstname, lastname, ign, image, country, facebook, twitter, steam from Players WHERE pid=?) AS T 
            ON T.pid = Belongs_to.pid) AS TT 
        ON Teams.tid = TT.tid');
        $stmt->bind_param('s', $pid);
        $stmt->execute();
        $results = $stmt->get_result();
        $row = $results->fetch_assoc();
        if(!$row){echo "<center>No player found for the given player id</center>"; return;}
        echo "<title> ".$row['ign']."</title>";
        $flag = str_replace(' ', '', (strtolower($row["country"])));
        $tflag = str_replace(' ', '', (strtolower($row["tcountry"])));
    ?>
    <img style="vertical-align:top; height:300px; width:300px" 
    src="<?php echo $row['image'] ?>"
    onerror="this.src = 'teamlogos/default.png';">    
        </div>
    <?php
        echo "<div class='wrapright2'><center><span id='title'>".$row['firstname']." '".$row['ign']."' ".$row['lastname']. "</center></span>" ;
        echo "<b>Country: </b> <img src=flags/".$flag. ".png> " .$row['country']. "<br/>";
        echo "<b>Primary team: </b><img src='flags/".$tflag.".png'> <a href='teamid.php?tid=".$row['tid']."'>".$row['name']."</a><br/>";
        echo "<div>";
        if($row['facebook']){echo "<a href=".$row['facebook']."><img style='margin-left: 7px; float: left;' src='imgs/facebook.png' ></a>";}
        if($row['twitter']){echo "<a href=".$row['twitter']."><img style='margin-left: 7px; float: left;' src='imgs/twitter.png' ></a>";}
        if($row['steam']){echo "<a href=".$row['steam']."><img style='margin-left: 7px; float: left;' src='imgs/steam.png' ></a>";}
        if($row['tid'] != 40){
        echo "<br/><b>Teammates: </b><br/>";

echo "<div class='table table-hover' style='max-width: 400px;'>";
$sql = "SELECT Players.pid, country, ign From Players
INNER JOIN
(
SELECT * From Belongs_to WHERE Belongs_to.tid =(SELECT Teams.tid FROM Teams
RIGHT JOIN
(SELECT Players.pid, tid, ign, country FROM Players
LEFT JOIN Belongs_to ON Players.pid = Belongs_to.pid WHERE Players.pid = $pid AND prim = 1) AS T ON Teams.tid = T.tid)) AS F
ON Players.pid = F.pid
WHERE Players.pid != $pid LIMIT 4";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table class='table'>";
    while($row = $result->fetch_assoc()) {
        $flag = str_replace(' ', '', (strtolower($row["country"])));
        echo "<tr><td width='600px'' height='20px'><img src='flags/".$flag.".png' title='".$row["country"]."''> <a href='playerid.php?pid=".$row["pid"]."'>".$row["ign"]."</td></a>";
    }

    echo "</table>";
} else {
    echo "This team does not have any players";
}}

$conn->close();

?>
</div></div></div></div>
</body>
<?php include_once("footer.php"); ?>
</html>