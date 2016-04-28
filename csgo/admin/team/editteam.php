<html>
    <head>
  		<?php include_once "../navbar.php"; 
  			  include_once "../connection.php";
  		?>

        <title>Update teaminfo</title>
        <link href="<?php echo "//" . $url . "/css/insert.css"; ?>" rel="stylesheet">
        <link href="<?php echo "//" . $url . "/css/style.css"; ?>" rel="stylesheet">
        <link href="<?php echo "//" . $url . "/bootstrap/css/bootstrap.css"; ?>" rel="stylesheet">
    </head>
    <body>
<div class="wrap">
	<div class="container2">
<?php
$tid = $_GET['tid'];
if(isset($_POST['submit']))
{
	$sql = "UPDATE Teams
			SET name = 			'".$_POST['name']."', 
				tcountry =  	'".$_POST['country']."',
				logo      = 	'".$_POST['logo']."',
				hltvname =  		'".$_POST['hltv']."',
				csglname =  		'".$_POST['csgl']."',
				active =  		'".$_POST['active']."'
			WHERE tid = '$tid'" ;
	        $results = $conn->query($sql);
	$sql2 = "SELECT * FROM Teams WHERE tid = '$tid'";
    $results = $conn->query($sql2);
	if($results === FALSE) { 
	    die(mysqli_error());}
    $row = $results->fetch_assoc();
    if($row['active'] == 1){$active = "checked";}
  	else{$active = "";}
	?>
	<div class="wrapleft"><img style="height:300px; width:300px;" src='<?php echo $url . "/" . $row['logo'] ?>' onerror="this.src='<?php echo $url . "/playerlogos/default.png" ?>'">
		<center><a href="selectteam.php">Back to teamselection</a></center></div>
	<div class="wrapright"><span style="display:inline-block">
	<form action="editteam.php?tid=<?php echo $tid ?>" method="post">
	<h1 style="text-align: center;">Edit team '<?php echo $row['name'] ?>' </h1>
	Name: <input class="input" type="text" name="name" value="<?php echo $row['name'] ?>"><br>
	Country: <input class="input" type="text" name="country" value="<?php echo $row['tcountry'] ?>"><br>
	Logo: <input class="input" type="text" name="logo" value="<?php echo $row['logo'] ?>"><br>
	HLTV name: <input class="input" type="text" name="hltv" value="<?php echo $row['hltvname'] ?>"><br>
	CSGL name: <input class="input" type="text" name="csgl" value="<?php echo $row['csglname'] ?>"><br>
	<input name="active" value="0" type="hidden">
	Active: <input type="checkbox" name="active" value="1" <?php echo $active ?>><br>
	<input name="submit" type="submit"  value=" Submit "  id="submit"  class="submit"/>
	<center>Teaminfo has been updated!</center>
	</form></div>
	<?php
}
else
{
	$sql2 = "SELECT * FROM Teams WHERE tid = '$tid'";
    $results = $conn->query($sql2);
	if($results === FALSE) { 
	    die(mysqli_error());}
    $row = $results->fetch_assoc();
  	if($row['active'] == 1){$active = "checked";}
  	else{$active = "";}
	?>
	<div class="wrapleft"><img style="height:300px; width:300px;" src='<?php echo $url . "/" . $row['logo'] ?>' onerror="this.src='<?php echo $url . "/playerlogos/default.png" ?>'">
		<center><a href="selectteam.php">Back to teamselection</a></center></div>
	<div class="wrapright"><span style="display:inline-block">
	<form action="editteam.php?tid=<?php echo $tid ?>" method="post">
	<h1 style="text-align: center;">Edit team '<?php echo $row['name'] ?>' </h1>
	Name: <input class="input" type="text" name="name" value="<?php echo $row['name'] ?>"><br>
	Country: <input class="input" type="text" name="country" value="<?php echo $row['tcountry'] ?>"><br>
	Logo: <input class="input" type="text" name="logo" value="<?php echo $row['logo'] ?>"><br>
	HLTV name: <input class="input" type="text" name="hltv" value="<?php echo $row['hltvname'] ?>"><br>
	CSGL name: <input class="input" type="text" name="csgl" value="<?php echo $row['csglname'] ?>"><br>
			   <input name="active" value="0" type="hidden">
	Active: <input type="checkbox" name="active" value="1" <?php echo $active ?>><br>
	<input name="submit" type="submit"  value=" Submit "  id="submit"  class="submit"/>
	</form></div>
<?php
}
?>
</div></div>
    </body>
</html>