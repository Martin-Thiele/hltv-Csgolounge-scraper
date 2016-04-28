<html>
    <head>
  		<?php include_once ("../navbar.php"); 
  			  include_once ("../connection.php");
  		?>
    </head>
        <title>Add player to team</title>
        <link href="<?php echo "//" . $url . "/css/insert.css"; ?>" rel="stylesheet">
        <link href="<?php echo "//" . $url . "/css/style.css"; ?>" rel="stylesheet">
        <link href="<?php echo "//" . $url . "/bootstrap/css/bootstrap.css"; ?>" rel="stylesheet">
   		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	    <script src="<?php echo "//" . $url . "/typeahead/typeahead.min.js" ?>"></script>
	    <script src="<?php echo "//" . $url . "/typeahead/typeahead.js" ?>"></script>
    </head>
    <body>
			<div class="wrap">
			<div class="form_div">
				<form action="playerteam.php" method="post">    <!-- method can be set POST for hiding values in URL-->
					<h1 style="text-align: center">Add player to team</h1>
					<label>Player:</label>
					<br />
					<input class="typeahead4 tt-query" autofocus type="text" spellcheck="false" autcomplete="off" autofocus name="ign" value="" />
					<br />
					<label>Team:</label><br />        
					<input class="typeahead tt-query" autofocus type="text" spellcheck="false" autcomplete="off" autofocus name="team" value="" />
					<br /> <label>Primary:</label>
					<input name="prim" value="0" type="hidden">
					<input type="checkbox" name="prim" value="1" checked><br>
					<input class="submit" type="submit" name="submit" value="Add" />
	<?php
		if(isset($_POST['submit'])){
	    $ign = ($_POST['ign']);
	    $team = ($_POST['team']);
	    $prim = $_POST['prim'];
		$sql = "SELECT pid FROM Players WHERE ign='$ign' limit 1";
		$sql2 = "SELECT tid FROM Teams WHERE name='$team' limit 1";
		$results = $conn->query($sql);
		$value = $results->fetch_assoc();
		$results2 = $conn->query($sql2);
		$value2 = $results2->fetch_assoc();
		$sql3 = "SELECT prim FROM Belongs_to WHERE pid='".$value['pid']."' limit 1";
		$results3 = $conn->query($sql3);
		$value3 = $results3->fetch_assoc();

		if($value3['prim'] == 1 AND $prim == 1){echo "Player already has a primary team";}
		else{
	    $query = "insert into Belongs_to(pid, tid, prim) values (".$value['pid'].",".$value2['tid'].", ".$prim.")";
		$addplayer = $conn->query($query);
		if (!$addplayer){echo mysqli_error($conn);}
		else if($value3['prim'] == 1 AND $prim == 0){echo ucfirst("<center><br/><br/>$ign has been added to $team as a standin</center>");}
		else{echo ucfirst("$ign has been added to $team as a primary player");}
		}
	

	    }
	?>					
					</form>
				</div><br/>
	<div class="container2">
	<div class="wraplatestleft">
				<?php
	$sql = "SELECT ign, country FROM Players
	LEFT OUTER JOIN Belongs_to
	ON Players.pid = Belongs_to.pid
	WHERE Belongs_to.tid IS null;";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
	    echo "<table class='table table-hover'><thead><tr><td><b>Players missing team:</b></td></tr></thead>";
	    // output data of each row
	    while($row = $result->fetch_assoc()) {
	    	$flag = str_replace(' ', '', (strtolower($row["country"])));
	        echo "<tr><td><img src='//".$url."/flags/".$flag.".png''> " .$row["ign"]."</td></tr>";
	    }
	    echo "</table>";
	} else {
	    echo "No players are missing a team!";
	}
	echo "</div><div class='wraplatestright'>";	
	$sql = "SELECT name, tid, tcountry, active FROM Teams WHERE tid != 40";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
	    echo "<table class='table table-hover' cellspacing ='0'><tr><td><b>Teams missing players:</td></b><td>#</td></tr>";
	    // output data of each row
	    while($row = $result->fetch_assoc()) {

		    $sqlay = "SELECT COUNT(*) AS count FROM Matches WHERE tid_1 = '".$row['tid']."' OR tid_2 = '".$row['tid']."'";
		   	$resultay = $conn->query($sqlay);
		   	$roway = $resultay->fetch_assoc();
	   	if($roway["count"] >= 10){
		   	$sql2 = "SELECT COUNT(*) AS count FROM Belongs_to WHERE tid = '".$row['tid']."'";
		   	$result2 = $conn->query($sql2);
		   	$row2 = $result2->fetch_assoc();
		   	$missing = 5-$row2["count"];
		   	if(($row2["count"] < 5 OR $row2["count"] > 5) AND $row["active"] == 1){
			    $flag = str_replace(' ', '', (strtolower($row["tcountry"])));
			    echo "<tr><td><img src='//".$url."/flags/".$flag.".png''> <a href='//".$url."/teamid.php?tid=".$row['tid']."'>".$row["name"]."</a></td>
			    <td> ".$missing."</td>
			    </tr>";
		    }
		}
	}
	    echo "</table>";
	} else {
	    echo "No Teams are missing a players!";
	}
	echo "</div></div>";


	$conn->close();
?>
			</div>
    </body>
</html>