<html>
    <head>
  		<?php include_once "../navbar.php"; 
  			  include_once "../connection.php";
  		?>
    </head>
        <title>Assign subcomp to competition</title>
        <link href="<?php echo "//" . $url . "/css/insert.css"; ?>" rel="stylesheet">
        <link href="<?php echo "//" . $url . "/css/style.css"; ?>" rel="stylesheet">
        <link href="<?php echo "//" . $url . "/bootstrap/css/bootstrap.css"; ?>" rel="stylesheet">
   		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	    <script src="<?php echo "//" . $url . "/typeahead/typeahead.min.js" ?>"></script>
	    <script src="<?php echo "//" . $url . "/typeahead/typeahead.js" ?>"></script>
    </head>
    <body>

			<div class="wrap">
			<!--HTML form -->
				<div class="form_div">
					<form action="assigncomp.php" method="post">    <!-- method can be set POST for hiding values in URL-->
						<h1>Assign subcomp to competition</h1>
						<label>Subcomp:</label><br/>
						<input class="typeahead5 tt-query" autofocus type="text" spellcheck="false" autcomplete="off" name="sub" value="" />

						<label>Competition:</label><br />        
						<input class="typeahead6 tt-query" type="text" spellcheck="false" autcomplete="off" name="comp" value="" />

						<input class="submit" type="submit" name="submit" value="Add" />
	<?php

		if(isset($_POST['submit'])){
		$sub = str_replace("'","''", (strtolower($_POST['sub'])));
	    $comp = str_replace("'","''", (strtolower($_POST['comp'])));
		$sql = "SELECT subcid FROM Subcomp WHERE name='$sub' limit 1";
		$sql2 = "SELECT cid FROM Competitions WHERE name='$comp' limit 1";
        $results = $conn->query($sql);
        $value = $results->fetch_assoc();
        $results2 = $conn->query($sql2);
        $value2 = $results2->fetch_assoc();
		if(!$value && !$value2){echo "<center>The subcompetition '$sub' does not exist!<br/>
											 The competition '$comp' does not exist!</center>";}
		else{
			if(!$value){echo "<center>The subcompetition '$sub' does not exist!<br/></center>";}
			else{
				if(!$value2){echo "<center>The competition '$comp' does not exist!<br/></center>";}
				else{
					$_SESSION['subcid'] = $value['subcid'];
					$_SESSION['cid'] = $value2['cid'];
		}
		}}
	    $query = "INSERT INTO Comp_belongs_to(subcid, cid) VALUES ('".$_SESSION['subcid']."','".$_SESSION['cid']."')";
	    $insert = $conn->query($query);
		if (!$insert){echo "<center>" . mysqli_error($conn) ." </center>";} else{echo ucfirst("<center>$sub has been added to $comp</center>");}
	    }
	?>					
					</form>
				</div>
				<?php
	$sql = "SELECT name, Subcomp.subcid FROM Subcomp
	LEFT OUTER JOIN Comp_belongs_to
	ON Subcomp.subcid = Comp_belongs_to.subcid
	WHERE Comp_belongs_to.cid IS null;";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
	    echo "<table><tr><td><b>Subcompetitions missing competitions:</b></td></tr>";
	    // output data of each row
	    while($row = $result->fetch_assoc()) {
	        echo "<tr><td>".$row["name"]."</td>";
	    }
	    echo "</table>";
	} else {
	    echo "0 results";
	}
	$conn->close();
?>
			</div>
    </body>
</html>