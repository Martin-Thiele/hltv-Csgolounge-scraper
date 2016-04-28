<html>
    <head>
  		<?php include_once "../navbar.php"; 
  		include_once "../connection.php"
  		?>
        <title>Update playerinfo</title>
        <link href="<?php echo "//" . $url . "/css/insert.css"; ?>" rel="stylesheet">
        <link href="<?php echo "//" . $url . "/css/style.css"; ?>" rel="stylesheet">
        <link href="<?php echo "//" . $url . "/bootstrap/css/bootstrap.css"; ?>" rel="stylesheet">
	    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	    <script src="<?php echo "//" . $url . "/typeahead/typeahead.min.js" ?>"></script>
	    <script src="<?php echo "//" . $url . "/typeahead/typeahead.js" ?>"></script>
    </head>
    <body>
<div class="wrap">
<?php
$id = $_GET['id'];
if(isset($_POST['submit']))
{
	$upDate = date("Y-m-d", strtotime($_POST['transdate']));
	$sql = "UPDATE Playertransfers
			SET 
			transdate   = '".$upDate."',
			tid1	    = (SELECT tid FROM Teams WHERE name='".$_POST['team1']."'),
			tid2    	= (SELECT tid FROM Teams WHERE name='".$_POST['team2']."')
			WHERE id       = '$id'" ;
	$retval = $conn->query($sql);
	if(!$retval )
		{
		  die('Could not update data: ' . mysqli_error($conn));
		}

	$sql2 = "SELECT Players.pid, ign, country, tid1, tid2, namex, name, tcountryx, tcountry, transdate, id FROM Players
INNER JOIN
(SELECT pid, tid1, tid2, namex, name, tcountryx, tcountry, transdate, id FROM Teams
INNER JOIN
(SELECT pid, tid AS tidx, tid1, tid2, name as namex, tcountry as tcountryx, transdate, id FROM Teams
INNER JOIN
(SELECT pid, tid1, tid2, transdate, id FROM Playertransfers WHERE id = '$id') AS T
ON Teams.tid = T.tid1) AS TT
ON Teams.tid = TT.tid2) AS TTT
ON Players.pid = TTT.pid";
	$result = $conn->query($sql2);
	if(!$result) {
	    die(mysqli_error($conn));}
	$row = $result->fetch_assoc();
    $newDate = date("d.m.Y", strtotime($row["transdate"]));
	?>

	<div class="wrapleft"><img style="height:300px; width:300px;" src='<?php echo $url . "/" . $row['image'] ?>' onerror="this.src='<?php echo $url . "/playerlogos/default.png" ?>'">
		<center><a href="selecttransfer.php">Back to transferselection</a></center></div>
	<div class="wrapright">
	<form action="edittransfer.php?id=<?php echo $id ?>" method="post">
	<h1>Edit transfer '<?php echo $row['ign'] ?>' </h1>
	<input type="hidden" name="ud_id" value="">
	Transferdate: <input class="input" type="text" name="transdate" value="<?php echo $newDate ?>"><br>
	Team 1: <br/>
	<input class="typeahead tt-query" type="text" name="team1" value="<?php echo $row['namex'] ?>"><br>
	Team 2: <br/><input class="typeahead tt-query" type="text" name="team2" value="<?php echo $row['name'] ?>"><br>

	<input name="submit" type="submit"  value=" Submit "  id="submit"  class="submit"/>
	</form></div>
	<?php
}
else
{

	$sql2 = "SELECT Players.pid, ign, country, tid1, tid2, namex, name, tcountryx, tcountry, transdate, id FROM Players
INNER JOIN
(SELECT pid, tid1, tid2, namex, name, tcountryx, tcountry, transdate, id FROM Teams
INNER JOIN
(SELECT pid, tid AS tidx, tid1, tid2, name as namex, tcountry as tcountryx, transdate, id FROM Teams
INNER JOIN
(SELECT pid, tid1, tid2, transdate, id FROM Playertransfers WHERE id = '$id') AS T
ON Teams.tid = T.tid1) AS TT
ON Teams.tid = TT.tid2) AS TTT
ON Players.pid = TTT.pid";
	$result = $conn->query($sql2);
	if(!$result) {
	    die(mysqli_error($conn));}
	$row = $result->fetch_assoc();
	$newDate = date("d.m.Y", strtotime($row["transdate"]));
	?>
	<div class="wrapleft"><img style="height:300px; width:300px;" src='<?php echo $url . "/" . $row['image'] ?>' onerror="this.src='<?php echo $url . "/playerlogos/default.png" ?>'">
		<center><a href="selecttransfer.php">Back to transferselection</a></center></div>
	<div class="wrapright">
	<form action="edittransfer.php?id=<?php echo $id ?>" method="post">
	<h1>Edit transfer '<?php echo $row['ign'] ?>' </h1>
	<input type="hidden" name="ud_id" value="">
	Transferdate: <input class="input" type="text" name="transdate" value="<?php echo $newDate ?>"><br>
	Team 1: <br/>
	<input class="typeahead tt-query" type="text" name="team1" value="<?php echo $row['namex'] ?>"><br>
	Team 2: <br/><input class="typeahead tt-query" type="text" name="team2" value="<?php echo $row['name'] ?>"><br>

	<input name="submit" type="submit"  value=" Submit "  id="submit"  class="submit"/>
	</form></div>
<?php
}
?>
</div></div>
    </body>
</html>