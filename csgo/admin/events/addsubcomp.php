<html>
    <head>
  		<?php include_once "../navbar.php"; 
  			  include_once "../connection.php";
  		?>
        <title>Add subcompetition to database</title>
        <link href="<?php echo "//" . $url . "/css/insert.css"; ?>" rel="stylesheet">
        <link href="<?php echo "//" . $url . "/css/style.css"; ?>" rel="stylesheet">
        <link href="<?php echo "//" . $url . "/bootstrap/css/bootstrap.css"; ?>" rel="stylesheet">
    </head>
    <body>
		<div class="wrap">
		<!--HTML form -->
				<form action="addsubcomp.php" method="post">    <!-- method can be set POST for hiding values in URL-->
					<h2>Add subcompetition</h2>
					<label>Name:</label>
					<br />
					<input class="input" type="text" name="name" value="" autofocus />
					<br />
					<input class="submit" type="submit" name="submit" value="Add" />
<?php
	//Establishing Connection with Server
	if(isset($_POST['submit'])){
	//Fetching variables of the form which travels in URL
    $name = str_replace("'","''", ($_POST['name']));
    $logo = str_replace(' ', '', (strtolower($name)));
    if($name !=''){
	//Insert Query of SQL
    $query = "insert into Subcomp(subcid, name) values (DEFAULT, '$name')";
    $results = $conn->query($query);
	if (!$results){echo "<center>" . mysqli_error($conn) ." </center>";} else{echo ucfirst("<center>$name has been added to the database</center>");}
    }
	}
	//Closing Connection with Server
	$conn->close();
?>					
				</form>
			</div>
		</div>
    </body>
</html>