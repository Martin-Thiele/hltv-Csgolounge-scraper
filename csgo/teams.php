<html>
<head>
<?php
error_reporting(0);
include_once "navbar.php";
include_once "connection.php";
?>
<title>Teams</title>
<link rel="stylesheet" type="text/css" href="css/style.css">
<link href="bootstrap/css/bootstrap.css" rel="stylesheet">
</head>

<body>
<div class="wrap">
<h1>Teams</h1>
<div class="table table-hover" >
<?php
$num_rec_per_page=200;
if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; }; 
$start_from = ($page-1) * $num_rec_per_page;
$sql = "SELECT tid, name, tcountry, logo FROM Teams WHERE tid<>40 AND tid <> 108 ORDER BY name asc LIMIT $start_from, $num_rec_per_page";
$result = $conn->query($sql);

$sql2 = "SELECT tid FROM Teams";
$result2 = $conn->query($sql2);
$total_records = mysqli_num_rows($result2);  //count number of records
$total_pages = ceil($total_records / $num_rec_per_page); // count number of pages
$nextpage = $page+1;
$prevpage = $page-1;
echo "<div style='text-align: center; width: 100%;'>";

  // Goto 1st page  
    if($page == 1){
        echo "
        <div style='float: left'>
        <a id='unavbtn' href='#'>First page</a>
        </div>";
    }
    else {
        echo "
        <div style='float: left'>
        <a id='btn' class='btn' href='teams.php?page=1'>First page</a>
        </div> ";  
    }
    echo "<div style='margin: 0 auto; display: inline-block;'>";

    // Previous page
    if($page == 1){
        echo "<a id='unavbtn' href='#'>Previous page</a>";
    }

    else {
      echo "<a id='btn' href='teams.php?page=".$prevpage."'>Previous page</a>";
    }
    echo " ";

    // Next page
    if($page == $total_pages)
    {
      echo "<a id='unavbtn' href='#'>Next page</a>";
    }
    else{
        echo "<a id='btn' href='teams.php?page=".$nextpage."'>Next page</a>";
    }
    echo "<br/>Page ".$page." of ".$total_pages."</div>";

  // Goto last page 
    if($page == $total_pages)
    {
        echo "
        <div style='float: right;'>
        <a id='unavbtn' href='#'>Last page</a>
        </div>";
    }
    else {
        echo "
        <div style='float: right;'>
        <a id='btn' class='btn' href='teams.php?page=".$total_pages."'>Last page</a>
        </div>";
    }

echo "</div>";  

if ($result->num_rows > 0) {
    echo "<table class='table table-hover'>";
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $teamflag = str_replace(' ', '', (strtolower($row["tcountry"])));
        echo "<tr><td width='900px'><img src='flags/".$teamflag.".png''> <a href='teamid.php?tid=".$row["tid"]."'>".$row["name"]."</td></tr>";
    }
    echo "</table>";
} else {
    echo "0 results";
}
echo "<div style='text-align: center; width: 100%;'>";

  // Goto 1st page  
    if($page == 1){
        echo "
        <div style='float: left'>
        <a id='unavbtn' href='#'>First page</a>
        </div>";
    }
    else {
        echo "
        <div style='float: left'>
        <a id='btn' class='btn' href='teams.php?page=1'>First page</a>
        </div> ";  
    }
    echo "<div style='margin: 0 auto; display: inline-block;'>";

    // Previous page
    if($page == 1){
        echo "<a id='unavbtn' href='#'>Previous page</a>";
    }

    else {
      echo "<a id='btn' href='teams.php?page=".$prevpage."'>Previous page</a>";
    }
    echo " ";

    // Next page
    if($page == $total_pages)
    {
      echo "<a id='unavbtn' href='#'>Next page</a>";
    }
    else{
        echo "<a id='btn' href='teams.php?page=".$nextpage."'>Next page</a>";
    }
    echo "<br/>Page ".$page." of ".$total_pages."</div>";

  // Goto last page 
    if($page == $total_pages)
    {
        echo "
        <div style='float: right;'>
        <a id='unavbtn' href='#'>Last page</a>
        </div>";
    }
    else {
        echo "
        <div style='float: right;'>
        <a id='btn' class='btn' href='teams.php?page=".$total_pages."'>Last page</a>
        </div>";
    }

echo "</div>";  

$conn->close();
?>

</div>
</div>
<?php include_once("footer.php") ?>
</body>

</html>