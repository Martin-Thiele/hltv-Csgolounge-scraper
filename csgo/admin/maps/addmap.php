<html>
    <head>
  		<?php include_once "../navbar.php"; 
  			  include_once "../connection.php";
  		?>
        <title>Add map to database</title>
        <link href="<?php echo "//" . $url . "/css/insert.css"; ?>" rel="stylesheet">
        <link href="<?php echo "//" . $url . "/css/style.css"; ?>" rel="stylesheet">
        <link href="<?php echo "//" . $url . "/bootstrap/css/bootstrap.css"; ?>" rel="stylesheet">
    </head>
    <body>
		<div class="wrap">
		<!--HTML form -->
			<div class="form_div">
				<form action="addmap.php" method="post">    <!-- method can be set POST for hiding values in URL-->
					<h2>Add Map</h2>
					<label>Name:</label>
					<br />
					<input class="input" type="text" name="name" value="" autofocus />
					<br />
					<input class="submit" type="submit" name="submit" value="Add" />
<?php
	if(isset($_POST['submit'])){
    $name = (($_POST['name']));
    echo $name;
    if($name !=''){
    $query = "INSERT INTO Maps (mapid, name) VALUES ('DEFAULT', '".$name."')";
    $result = $conn->query($query);
    if(!$result){echo mysqli_error($conn);}
    echo "Woop";
	}
	else{echo "<center>Please specify a name</center>";}
}
	$conn->close();
?>					
				</form>
			</div>
		</div>
    </body>
</html>