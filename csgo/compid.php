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
$cid = ($_GET['cid']);
    $stmt = $conn->prepare('SELECT * FROM Competitions WHERE cid = ? LIMIT 1');
    $stmt->bind_param('s', $cid);
    $stmt->execute();
    $results = $stmt->get_result();
    $row = $results->fetch_assoc();
    if(!$row){echo "<center>No competition found for the given competition id</center>"; return;}
        echo "<title>".$row["name"]."</title>";
        echo "<center><h1>".$row["name"]."</h1></center>";


        $stmt = $conn->prepare('SELECT name, T.subcid, cid FROM Subcomp
                  INNER JOIN(
                  SELECT subcid, cid FROM Comp_belongs_to WHERE cid = ?) AS T 
                  ON Subcomp.subcid = T.subcid ORDER BY subcid desc');
        $stmt->bind_param('s', $cid);
        $stmt->execute();
        $results = $stmt->get_result();

    echo "<table class='table'><thead><tr>
    </td><td><b>Name</b></td>
    <td style='text-align: center;'><b>Start</b></td>
    <td style='text-align: center;'><b> - </b></td>
    <td style='text-align: center;'><b>End</b></td>
    </tr></thead>";
    while($row = $results->fetch_assoc()) {
    $startendsql = "SELECT MIN(match_date) AS start, MAX(match_date) AS end 
    FROM Matches WHERE subcid = ".$row['subcid']."";
    $startendresults = $conn->query($startendsql);
    if(mysqli_num_rows($startendresults) == 0) {die(mysqli_error($conn));}
    $startend = $startendresults->fetch_assoc();


        echo "<tr>
        <td><a href='subcomp.php?subcid=".$row['subcid']."'>".$row['name']."</a></td>
        <td style='text-align: center;'>".date("d.m/y", strtotime($startend['start']))."</td>
        <td style='text-align: center;'> - </td>
        <td style='text-align: center;'>".date("d.m/y", strtotime($startend['end']))."</td>
        </tr>";
    }
    $conn->close();
?>

    </table>

</div></div>
<?php include_once("footer.php") ?>
</body>

</html>