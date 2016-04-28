<html>
    <head>
  		<?php include_once "../navbar.php"; 
  			  include_once "../connection.php";
  		?>
    </head>
        <title>Add BO1 match</title>
        <link href="<?php echo "//" . $url . "/css/insertmatch.css"; ?>" rel="stylesheet">
        <link href="<?php echo "//" . $url . "/css/style.css"; ?>" rel="stylesheet">
        <link href="<?php echo "//" . $url . "/bootstrap/css/bootstrap.css"; ?>" rel="stylesheet">
   		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	    <script src="<?php echo "//" . $url . "/typeahead/typeahead.min.js" ?>"></script>
	    <script src="<?php echo "//" . $url . "/typeahead/typeahead.js" ?>"></script>
    </head>
    <body>
		<div class="wrap">
				<form action="addbo1match.php" method="post">    <!-- method can be set POST for hiding values in URL-->
					<h1><center>Add BO1 match</center></h1>
					<div class="container3">
						<h4>Teams</h4>
						<div class="wrapteam" >
							<label>Subcompetition:</label><br/> 
							<input class="typeahead2 tt-query" type="text" name="subcomp" autofocus value="" autocomplete="off" />
						</div><br/>
						<div class="wrapteam">
							<label>Team 1:</label><br/>      
							<input class="typeahead tt-query" type="text" name="team1" value="" autocomplete="off" />
						</div>   
						<div class="wrapteam"> vs </div>
						<div class="wrapteam" >
							<label>Team 2:</label><br/> 
							<input class="typeahead tt-query" type="text" name="team2" value="" autocomplete="off" />
						</div>
					</div>
					<div class="container3">
						<h4>Date and time</h4>
						<div class="wrapteam">
							<label>Date:</label><br/>      
							<input class="input" type="text" name="date" placeholder="dd-mm-yyyy" autocomplete="off" />
						</div>
						<div class="wrapteam" >
							<label>Time:</label><br/> 
							<input class="input" type="text" placeholder="hh:mm" name="time" value="" autocomplete="off" />
						</div>
					</div>
					<div class="container3">
						<h4>Result</h4>
						<div class="wrapteam">
							<label>Map:</label><br/>      
							<input class="typeahead3" type="text" name="mapid" value="" autocomplete="off" />
						</div>   
						<br/>
						<div class="wrapteam">
							<label>Team 1 score:</label><br/>      
							<input class="input" type="text" name="score1" value="" autocomplete="off" />
						</div>
						<div class="wrapteam" >
							<label>Team 2 score:</label><br/> 
							<input class="input" type="text" name="score2" value="" autocomplete="off" />
						</div>
					</div>
					<div class="submitform">
					<input class="submit" type="submit" name="submit" value="Add" /></div>
<?php
if(isset($_POST['submit'])){

$date    = $newDate = date("Y-m-d", strtotime(($_POST['date'])));
$time    = ($_POST['time']);
$score1  = ($_POST['score1']);
$score2  = ($_POST['score2']);
$sql = "SELECT subcid FROM Subcomp WHERE name ='".$_POST['subcomp']."' limit 1 ";
$sql2 = "SELECT tid FROM Teams WHERE name='".$_POST['team1']."' limit 1 ";
$sql3 = "SELECT tid FROM Teams WHERE name='".$_POST['team2']."' limit 1 ";
$sql4 = "SELECT mapid FROM Maps WHERE name='".$_POST['mapid']."' limit 1 ";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$result2 = $conn->query($sql2);
$row2 = $result2->fetch_assoc();
$result3 = $conn->query($sql3);
$row3 = $result3->fetch_assoc();
$result4 = $conn->query($sql4);
$row4 = $result4->fetch_assoc();
if($score1 > $score2 AND $score1 > 14){$complete = 1;}
else if($score1 < $score2 AND $score2 > 14 ){$complete = 1;}
else{$complete = 0;}


if(!$score1 AND !$score2){
$insert = "INSERT INTO Matches (mid, tid_1, tid_2, match_date, time, mapid, score_1, score_2, subcid, complete) 
				VALUES('Default', '".$row2["tid"]."', '".$row3["tid"]."', '".$date."','".$time."','".$row4["mapid"]."',NULL,NULL,'".$row["subcid"]."', '".$complete."')";
}
else{
	$insert = "INSERT INTO Matches (mid, tid_1, tid_2, match_date, time, mapid, score_1, score_2, subcid, complete) 
				VALUES('Default', '".$row2["tid"]."', '".$row3["tid"]."', '".$date."','".$time."','".$row4["mapid"]."','".$score1."','".$score2."','".$row["subcid"]."', '".$complete."')";
}
if (mysqli_query($conn, $insert)) {
    echo "<center><br/>Match has successfully been added</center>";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}
$conn->close();
}
?>
					</form>
				</div>
    </body>
</html>