<html>
    <head>
  		<?php include_once "../navbar.php"; 
  			  include_once "../connection.php";
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

<div class="wrap2" style="height:300px;">
<?php
$pid = $_GET['pid'];
if(isset($_POST['submit']))
{

	$sql2 = "UPDATE Belongs_to
SET tid = (SELECT tid FROM Teams WHERE name='".$_POST['team']."')
WHERE pid = $pid" ;
	$result = $conn->query($sql2);
	if(!$result)
		{
		  die('Could not update data: ' . mysqli_error($conn));
		}
	$sql = "SELECT * From Teams
RIGHT JOIN
(SELECT Belongs_to.pid, tid, ign, image FROM Belongs_to
RIGHT JOIN
(SELECT * FROM Players WHERE pid = '$pid') AS T ON T.pid = Belongs_to.pid)
AS E ON E.tid = Teams.tid";
	$result = $conn->query($sql);
	$row = $result->fetch_assoc();


	?>
	<div class="wrapleft"><img style=' border-bottom-left-radius: 10px;border-top-left-radius: 10px;vertical-align:top;height:300px; width:300px' src='<?php echo $url . "/" . $row['image'] ?>' onerror="this.src='<?php echo $url . "/playerlogos/default.png" ?>'">
		</div>
	<div class="wrapright"><span style="display:inline-block">
	<form action="editteam.php?pid=<?php echo $pid ?>" method="post">
	<h1 style="text-align: center">Edit team for '<?php echo $row['ign'] ?>' </h1>
	<input type="hidden" name="ud_id" value="">
	<div style="text-align: center"><input class="typeahead tt-query" type="text" name="team" autofocus autocomplete="off" value="<?php echo $row['name'] ?>"></div><br>
	<input name="submit" type="submit"  value=" Submit "  id="submit"  class="submit"/>
	<center>Playerinfo has been updated!</center>
	</form><center><a href="selectplayer.php">Back to playerselection</a></div>
	<?php
}
else
{
	$sql = "SELECT * From Teams
INNER JOIN
(SELECT Belongs_to.pid, tid, ign, image FROM Belongs_to
INNER JOIN
(SELECT * FROM Players WHERE pid = '$pid') AS T ON T.pid = Belongs_to.pid)
AS E ON E.tid = Teams.tid";
	$result = $conn->query($sql);
	$row = $result->fetch_assoc();
	?>
	<div class="wrapleft"><img style=' border-bottom-left-radius: 10px;border-top-left-radius: 10px;vertical-align:top;height:300px; width:300px'" src='<?php echo $url . "/" . $row['image'] ?>' onerror="this.src='<?php echo $url . "/playerlogos/default.png" ?>'">
		</center></div>
	<div class="wrapright"><span style="display:inline-block">
	<form action="editteam.php?pid=<?php echo $pid ?>" method="post">
	<h1 style='text-align: center'>Edit team for '<?php echo $row['ign'] ?>' </h1>
	<input type="hidden" name="ud_id" value="">
	<div style="text-align: center"><input class="typeahead tt-query" type="text" autofocus autocomplete="off" name="team" value="<?php echo $row['name'] ?>"></div><br>
	<input name="submit" type="submit"  value=" Submit "  id="submit"  class="submit"/>
	</form><center><a href="selectplayer.php">Back to playerselection</a></div>
<?php
}
?>
</div>
    </body>
</html>