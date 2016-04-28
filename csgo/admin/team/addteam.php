<html>
    <head>
  		<?php include_once "../navbar.php"; 
  			  include_once "../connection.php";
  		?>

        <title>Add team to database</title>
        <link href="<?php echo "//" . $url . "/css/insert.css"; ?>" rel="stylesheet">
        <link href="<?php echo "//" . $url . "/css/style.css"; ?>" rel="stylesheet">
        <link href="<?php echo "//" . $url . "/bootstrap/css/bootstrap.css"; ?>" rel="stylesheet">
    </head>
    <body>
		<div class="wrap">
		<!--HTML form -->
			<div class="form_div">
				<form action="addteam.php" method="post">    <!-- method can be set POST for hiding values in URL-->
					<h1 style="text-align: center">Add team</h1>
					<label>Name:</label>
					<br />
					<input class="input" type="text" autofocus name="name" value="" />
					<br />
					<label>Country:</label><br />        
					<input class="input" type="text" name="country" value="" />
					<br />
					<input class="submit" type="submit" name="submit" value="Add" />
<?php

	if(isset($_POST['submit'])){
    $name = (($_POST['name']));
    $tcountry = $_POST['country'];
    $logo = str_replace(' ', '', (strtolower($name)));
    if($name !=''){
    $query = "insert into Teams(tid, name, tcountry, logo, active) values (DEFAULT, '$name', '$tcountry', 'teamlogos/$logo.png', 1)";
	$results = $conn->query($query);
	if (!$results) {
    die('Invalid query: ' . mysqli_error($conn));
} else{echo ucfirst("<center><br/><br/>$name has been added to the database</span></center>");}
    }
	}

?>					
				</form>
			</div>
		</div>
    </body>
</html>