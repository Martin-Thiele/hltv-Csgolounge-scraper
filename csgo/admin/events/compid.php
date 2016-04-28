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
<?php 
$cid = ($_GET['cid']);
    $resultsql = "SELECT * FROM Competitions WHERE cid = $cid LIMIT 1";
        $results = $conn->query($resultsql);
        if($results === FALSE) {die(mysqli_error($conn));}
        $row = $results->fetch_assoc();
        echo "<title>".$row["name"]."</title>";
        echo "<center><h1>".$row["name"]."</h1></center>";

    $belongsql = "SELECT * FROM Subcomp
                  INNER JOIN(
                  SELECT * FROM Comp_belongs_to WHERE cid = $cid) AS T 
                  ON Subcomp.subcid = T.subcid";
    $results = $conn->query($belongsql);
    if(mysqli_num_rows($results) == 0) {die(mysqli_error($conn));}
    echo "<table class='table'><thead><tr>
    <td width='20px;'><b>Subcid</b>
    </td><td><b>Name</b></td>
    <td style='text-align: center;'><b>Start</b></td>
    <td style='text-align: center;'><b> - </b></td>
    <td style='text-align: center;'><b>End</b></td>
    <td style='text-align: center;'><b>Edit</b></td>
    </tr></thead>";
    while($row = $results->fetch_assoc()) {
    $startendsql = "SELECT MIN(match_date) AS start, MAX(match_date) AS end 
    FROM Matches WHERE subcid = ".$row['subcid']."";
    $startendresults = $conn->query($startendsql);
    if(mysqli_num_rows($startendresults) == 0) {die(mysqli_error($conn));}
    $startend = $startendresults->fetch_assoc();


        echo "<tr>
        <td>".$row['subcid']."</td>
        <td><a href='subcomp.php?subcid=".$row['subcid']."'>".$row['name']."</a></td>
        <td style='text-align: center;'>".date("d.m/y", strtotime($startend['start']))."</td>
        <td style='text-align: center;'> - </td>
        <td style='text-align: center;'>".date("d.m/y", strtotime($startend['end']))."</td>
        <td style='text-align: center;'><a href='editsubcomp.php?subcid=".$row["subcid"]."'>Edit</a></td>
        </tr>";
    }
?>

    </table>

</div></div>
</body>

</html>