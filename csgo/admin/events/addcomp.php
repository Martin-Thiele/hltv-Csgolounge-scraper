<html>
    <head>
  		<?php include_once "../navbar.php"; 
  		include_once "../connection.php";
  		?>
        <title>Add competition to database</title>
        <link href="<?php echo "//" . $url . "/css/insert.css"; ?>" rel="stylesheet">
        <link href="<?php echo "//" . $url . "/css/style.css"; ?>" rel="stylesheet">
        <link href="<?php echo "//" . $url . "/bootstrap/css/bootstrap.css"; ?>" rel="stylesheet">
    </head>
    <body>
		<div class="wrap">
		<!--HTML form -->
				<form action="addcomp.php" method="post">    <!-- method can be set POST for hiding values in URL-->
					<h1 style="text-align: center;">Add competition</h1>
					<label>Name:</label>
					<br />
					<input class="input" type="text" name="name" value="" autofocus />
					<br />
					<input class="submit" type="submit" name="submit" value="Add" />
<?php

	if(isset($_POST['submit'])){
	//Fetching variables of the form which travels in URL
    $name = str_replace("'","''", ($_POST['name']));
    $logo = str_replace(' ', '', (strtolower($name)));
    if($name !=''){
	//Insert Query of SQL
    $query = "insert into Competitions(cid, name) values (DEFAULT, '$name')";
    $result = $conn->query($query);
	if (mysqli_error($conn)){echo "<center>" . mysql_error($conn) ." </center>";} else{echo ucfirst("<center><br/><br/>$name has been added to the database</span></center>");}
    }
    else{
    echo "<p>Insertion Failed <br/> Some Fields are Blank!</p>";   
    }
	}
	//Closing Connection with Server
	$conn->close();
?>					
				</form>
		</div>
    </body>
</html>