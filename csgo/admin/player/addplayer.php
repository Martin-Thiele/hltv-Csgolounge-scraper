<html>
    <head>
  		<?php include_once "../navbar.php"; 
  			  include_once "../connection.php";
  		?>
        <title>Add player to database</title>
        <link href="<?php echo "//" . $url . "/css/insert.css"; ?>" rel="stylesheet">
        <link href="<?php echo "//" . $url . "/css/style.css"; ?>" rel="stylesheet">
        <link href="<?php echo "//" . $url . "/bootstrap/css/bootstrap.css"; ?>" rel="stylesheet">
    </head>
    <body>
		<div class="wrap">
			<div class="form_div">
				<form action="addplayer.php" method="post">    <!-- method can be set POST for hiding values in URL-->
					<h1 style="text-align: center">Add player</h1>
					<label>First name:</label>
					<br />
					<input class="input" type="text" autofocus name="first" value="" autocomplete="off" />
					<br />
					<label>Last name:</label><br />        
					<input class="input" type="text" name="last" value="" autocomplete="off" />
					<br />
					<label>Ingame name:</label><br />        
					<input class="input" type="text" name="ign" value="" autocomplete="off" />
					<br />
					<label>Country:</label><br />        
					<input class="input" type="text" name="country" value="" autocomplete="off" />
					<br />
					<label>Facebook:</label><br />        
					<input class="input" type="text" name="facebook" value="" autocomplete="off" />
					<br />
					<label>Twitter:</label><br />        
					<input class="input" type="text" name="twitter" value="" autocomplete="off" />
					<br />
					<label>Steam:</label><br />        
					<input class="input" type="text" name="steam" value="" autocomplete="off" />
					<br />
					<input class="submit" type="submit" name="submit" value="Add" /></div>
<?php

	if(isset($_POST['submit'])){
    $first = ($_POST['first']);
    $last = ($_POST['last']);
    $ign = ($_POST['ign']);
    $country = ($_POST['country']);
    $facebook = ($_POST['facebook']);
    $twitter = ($_POST['twitter']);
    $steam = ($_POST['steam']);
    $logo = str_replace(array("\\'", "'"), "", (str_replace(' ', '', (strtolower($ign)))));
    $firstfix = str_replace("'", "''", $first);
    $lastfix = str_replace("'", "''", $last);
    $ignfix = str_replace("'", "''", $ign);
    if($ign){
    $query = "insert into Players(pid, firstname, lastname, ign, country, image) values (DEFAULT, '$firstfix', '$lastfix', '$ignfix', '$country', 'playerlogos/$logo.png')";
	$go = $conn->query($query);
	if(!$go){echo mysqli_error($conn);}
	else{echo "".$ign. " Has been added to the database";}
	}}
?>					
				</form>
			</div>
		</div>
    </body>
</html>