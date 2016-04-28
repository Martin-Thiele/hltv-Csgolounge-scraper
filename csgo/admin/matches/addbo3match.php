<html>
    <head>
  		<?php include_once "../navbar.php"; 
  			  include_once "../connection.php";
  		?>
    </head>
        <title>Add BO3 match</title>
        <link href="<?php echo "//" . $url . "/css/insertmatch.css"; ?>" rel="stylesheet">
        <link href="<?php echo "//" . $url . "/css/style.css"; ?>" rel="stylesheet">
        <link href="<?php echo "//" . $url . "/bootstrap/css/bootstrap.css"; ?>" rel="stylesheet">
   		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	    <script src="<?php echo "//" . $url . "/typeahead/typeahead.min.js" ?>"></script>
	    <script src="<?php echo "//" . $url . "/typeahead/typeahead.js" ?>"></script>
    </head>
    <body>
		<div class="wrap">
				<form action="addbo3match.php" method="post">    <!-- method can be set POST for hiding values in URL-->
					<h1><center>Add BO3 match</center></h1>
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
<hr>
						<div class="wrapteam">
							<label>Map2:</label><br/>      
							<input class="typeahead3" type="text" name="mapid2" value="" autocomplete="off" />
						</div>   
						<br/>
						<div class="wrapteam">
							<label>Team 1 score:</label><br/>      
							<input class="input" type="text" name="score1_2" value="" autocomplete="off" />
						</div>
						<div class="wrapteam" >
							<label>Team 2 score:</label><br/> 
							<input class="input" type="text" name="score2_2" value="" autocomplete="off" />
						</div>
<hr>
						<div class="wrapteam">
							<label>Map3:</label><br/>      
							<input class="typeahead3" type="text" name="mapid3" value="" autocomplete="off" />
						</div>   
						<br/>
						<div class="wrapteam">
							<label>Team 1 score:</label><br/>      
							<input class="input" type="text" name="score1_3" value="" autocomplete="off" />
						</div>
						<div class="wrapteam" >
							<label>Team 2 score:</label><br/> 
							<input class="input" type="text" name="score2_3" value="" autocomplete="off" />
						</div>
					</div>
					<div class="submitform">
					<input class="submit" type="submit" name="submit" value="Add" /></div>
<?php
if(isset($_POST['submit'])){
$subcomp = ($_POST['subcomp']);
$team1   = ($_POST['team1']);
$team2   = ($_POST['team2']);
$date    = $newDate = date("Y-m-d", strtotime(($_POST['date'])));
$time    = ($_POST['time']);
$mapid   = ($_POST['mapid']);
$score1  = ($_POST['score1']);
$score2  = ($_POST['score2']);
$mapid2   = ($_POST['mapid2']);
$score1_2  = ($_POST['score1_2']);
$score2_2  = ($_POST['score2_2']);
$mapid3   = ($_POST['mapid3']);
$score1_3  = ($_POST['score1_3']);
$score2_3  = ($_POST['score2_3']);
$sql = "SELECT subcid FROM Subcomp WHERE name ='$subcomp' limit 1 ";
$sql2 = "SELECT tid FROM Teams WHERE name='$team1' limit 1 ";
$sql3 = "SELECT tid FROM Teams WHERE name='$team2' limit 1 ";
$sql4 = "SELECT mapid FROM Maps WHERE name='$mapid' limit 1 ";
$sql5 = "SELECT mapid FROM Maps WHERE name='$mapid2' limit 1 ";
$sql6 = "SELECT mapid FROM Maps WHERE name='$mapid3' limit 1 ";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$result2 = $conn->query($sql2);
$row2 = $result2->fetch_assoc();
$result3 = $conn->query($sql3);
$row3 = $result3->fetch_assoc();
$result4 = $conn->query($sql4);
$row4 = $result4->fetch_assoc();
$result5 = $conn->query($sql5);
$row5 = $result5->fetch_assoc();
$result6 = $conn->query($sql6);
$row6 = $result6->fetch_assoc();

$i = 0;
$j = 0;
if($score1 > $score2 AND $score1 > 14){$i++; $complete = 1;}
else if($score1 < $score2 AND $score2 > 14 ){$j++; $complete = 1;}
else{$complete = 0;}
if($score1_2 > $score2_2 AND $score1_2 > 14 ){$i++; $complete2 = 1;}
else if($score1_2 < $score2_2 AND $score2_2 > 14){$j++; $complete2 = 1;}
else{$complete2 = 0;}
if($score1_3 > $score2_3 AND $score1_3 > 14){$i++; $complete3 = 1;}
else if($score1_3 < $score2_3 AND $score2_3 > 14){$j++; $complete3 = 1;}
else{$complete3 = 0;}
if($i == 2 OR $j == 2){$complete3 = 1;}


$insert = "INSERT INTO Matches (mid, tid_1, tid_2, match_date, time, mapid, score_1, score_2, subcid, complete) 
				VALUES('Default', '".$row2["tid"]."', '".$row3["tid"]."', '".$date."','".$time."','".$row4["mapid"]."','".$score1."','".$score2."','".$row["subcid"]."', '".$complete."'),
					  ('Default', '".$row2["tid"]."', '".$row3["tid"]."', '".$date."','".$time."','".$row5["mapid"]."','".$score1_2."','".$score2_2."','".$row["subcid"]."', '".$complete2."'),
					  ('Default', '".$row2["tid"]."', '".$row3["tid"]."', '".$date."','".$time."','".$row6["mapid"]."','".$score1_3."','".$score2_3."','".$row["subcid"]."', '".$complete3."')";

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