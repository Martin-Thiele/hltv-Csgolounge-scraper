<html>
    <head>
  		<?php include_once "../navbar.php"; 
  		include_once "../connection.php"
  		?>
        <title>Update playerinfo</title>
        <link href="<?php echo "//" . $url . "/css/insert.css"; ?>" rel="stylesheet">
        <link href="<?php echo "//" . $url . "/css/style.css"; ?>" rel="stylesheet">
        <link href="<?php echo "//" . $url . "/bootstrap/css/bootstrap.css"; ?>" rel="stylesheet">
    </head>
    <body>
<div class="wrap">
<?php
$pid = $_GET['pid'];

if(isset($_POST['submit']))
{
	// Errorhandling for name O'brien
	$sql = "UPDATE Players
			SET firstname   = '".$_POST['firstname']."',
				lastname    =  '".$_POST['lastname']."',
				ign         = 		'".$_POST['ign']."',
				country     = 	'".$_POST['country']."',
				image       = 	'".$_POST['image']."',
				facebook    = 	'".$_POST['facebook']."',
				twitter     = 	'".$_POST['twitter']."',
				steam       = 	'".$_POST['steam']."'
			WHERE pid       = '$pid'" ;


	$retval = $conn->query($sql);
	if(!$retval )
		{
		  die('Could not update data: ' . mysqli_error($conn));
		}

	$sql2 = "SELECT * FROM Players WHERE pid = '$pid'";
	$result = $conn->query($sql2);
	if(!$result) {
	    die(mysqli_error($conn));}
	$row = $result->fetch_assoc();
	?>
	<div class="wrapleft"><img style="height:300px; width:300px;" src='<?php echo $url . "/" . $row['image'] ?>' onerror="this.src='<?php echo $url . "/playerlogos/default.png" ?>'">
		<center><a href="selectplayer.php">Back to playerselection</a></center></div>
	<div class="wrapright">
	<form action="editplayer.php?pid=<?php echo $pid ?>" method="post">
	<h1>Edit player '<?php echo $row['ign'] ?>' </h1>
	First name: <input class="input" type="text" name="firstname" value="<?php echo $row['firstname'] ?>"><br>
	Last name: <input class="input" type="text" name="lastname" value="<?php echo $row['lastname'] ?>"><br>
	playername: <br/> <input class="input" type="text" name="ign" value="<?php echo $row['ign'] ?>"><br>
	Country: <input class="input" type="text" name="country" value="<?php echo $row['country'] ?>"><br>
	Image: <input class="input" type="text" name="image" value="<?php echo $row['image'] ?>"><br>
	Facebook: <input class="input" type="text" name="facebook" value="<?php echo $row['facebook'] ?>"><br>
	Twitter: <input class="input" type="text" name="twitter" value="<?php echo $row['twitter'] ?>"><br>
	Steam: <input class="input" type="text" name="steam" value="<?php echo $row['steam'] ?>"><br>
	<input name="submit" type="submit"  value=" Submit "  id="submit"  class="submit"/>
	<center>Playerinfo has been updated!</center>
	</form></div>
	<?php
}
else
{

	$sql2 = "SELECT * FROM Players WHERE pid = '$pid'";
	$result = $conn->query($sql2);
	if(!$result) {
	    die(mysqli_error($conn));}
	$row = $result->fetch_assoc();
	?>

	<div class="wrapleft"><img style="height:300px; width:300px;" src='<?php echo $url . "/" . $row['image'] ?>' onerror="this.src='<?php echo $url . "/playerlogos/default.png" ?>'">
		<center><a href="selectplayer.php">Back to playerselection</a></center></div>
	<div class="wrapright">
	<form action="editplayer.php?pid=<?php echo $pid ?>" method="post">
	<h1>Edit player '<?php echo $row['ign'] ?>' </h1>
	<input type="hidden" name="ud_id" value="">
	First name: <input class="input" type="text" name="firstname" value="<?php echo $row['firstname'] ?>"><br>
	Last name: <input class="input" type="text" name="lastname" value="<?php echo $row['lastname'] ?>"><br>
	playername: <input class="input" type="text" name="ign" value="<?php echo $row['ign'] ?>"><br>
	Country: <input class="input" type="text" name="country" value="<?php echo $row['country'] ?>"><br>
	Image: <input class="input" type="text" name="image" value="<?php echo $row['image'] ?>"><br>
	Facebook: <input class="input" type="text" name="facebook" value="<?php echo $row['facebook'] ?>"><br>
	Twitter: <input class="input" type="text" name="twitter" value="<?php echo $row['twitter'] ?>"><br>
	Steam: <input class="input" type="text" name="steam" value="<?php echo $row['steam'] ?>"><br>
	<input name="submit" type="submit"  value=" Submit "  id="submit"  class="submit"/>
	</form></div>
<?php
}
?>
</div></div>
    </body>
</html>