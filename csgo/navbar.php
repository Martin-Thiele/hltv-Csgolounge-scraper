<html>
<head>

	<link rel="shortcut icon" href="favicon.ico" />
    <link rel="stylesheet" type="text/css" href="css/navbar.css">
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Open+Sans" />


</head>
<body>
<center><img src="imgs/header.png" width="900px" height="200px"></center>
    <div class="nav">
<?php
if ($_SERVER['PHP_SELF'] != '/csgo/index.php')
{ $page1 = 'none'; } else { $page1 = 'active'; }
if ($_SERVER['PHP_SELF'] != '/csgo/players.php' && ($_SERVER['PHP_SELF'] !='/csgo/playerid.php') && ($_SERVER['PHP_SELF'] !='/csgo/transfers.php'))
{ $page2 = 'none'; } else { $page2 = 'active'; }
if ($_SERVER['PHP_SELF'] != '/csgo/teams.php' && ($_SERVER['PHP_SELF'] !='/csgo/teamid.php'))
{ $page3 = 'none'; } else { $page3 = 'active'; }
if ($_SERVER['PHP_SELF'] != '/csgo/results.php' && ($_SERVER['PHP_SELF'] != '/csgo/matchid.php'))
{ $page4 = 'none'; } else { $page4 = 'active'; }
if ($_SERVER['PHP_SELF'] != '/csgo/maps.php' && $_SERVER['PHP_SELF'] != '/csgo/mapid.php')
{ $page5 = 'none'; } else { $page5 = 'active'; }
if ($_SERVER['PHP_SELF'] != '/csgo/events.php' && $_SERVER['PHP_SELF'] != '/csgo/compid.php' && $_SERVER['PHP_SELF'] != '/csgo/subcomp.php')
{ $page6 = 'none'; } else { $page6 = 'active'; }

?>
<br>
<ul id="nav">
<li><a href="index.php" style="border-top-left-radius: 10px;" class="<?php echo $page1;?>">Matches</a></li>
<li><a href="players.php" class="<?php echo $page2;?>">Players</a></li>
<li><a href="teams.php" class="<?php echo $page3;?>">Teams</a></li>
<li><a href="results.php" class="<?php echo $page4;?>">Results</a></li>
<li><a href="maps.php" class="<?php echo $page5;?>">Maps</a></li>
<li><a href="events.php" style="border-top-right-radius: 10px;" class="<?php echo $page6;?>">Events</a></li>
</ul>
    </div>
</body>
</html>

