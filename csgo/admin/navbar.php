<html>
<head>
	<?php include_once("connection.php"); ?>
	<link rel="shortcut icon" href="<?php echo "//" .$url. "/favicon.ico"?>"/>
        <link href="<?php echo "//" . $url . "/css/navbar.css"; ?>" rel="stylesheet">
        <link href="<?php echo "//" . $url . "/css/style.css"; ?>" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Open+Sans" />
</head>
<body class="news">
<center><img src="<?php echo "//" . $url . "/imgs/header.png" ?>" width="900px" height="200px"></center>
    <div class="nav">
<?php
if ($_SERVER['PHP_SELF'] != $navurl . "X9E1x9zxji2l/index.php")
{ $page1 = 'none'; } else { $page1 = 'active'; }
if ($_SERVER['PHP_SELF'] != $navurl . "X9E1x9zxji2l/players.php") 
{ $page2 = 'none'; } else { $page2 = 'active'; }
if ($_SERVER['PHP_SELF'] != $navurl . "X9E1x9zxji2l/teams.php")
{ $page3 = 'none'; } else { $page3 = 'active'; }
if ($_SERVER['PHP_SELF'] != $navurl . "X9E1x9zxji2l/matches/results.php")
{ $page4 = 'none'; } else { $page4 = 'active'; }
if ($_SERVER['PHP_SELF'] != $navurl . "X9E1x9zxji2l/maps.php")
{ $page5 = 'none'; } else { $page5 = 'active'; }
if ($_SERVER['PHP_SELF'] != $navurl . "X9E1x9zxji2l/events.php")
{ $page6 = 'none'; } else { $page6 = 'active'; }
?>
<center><a href="<?php echo $url; ?>">Back to userpage</a></center>
<ul id="nav">
<li class="logo"><a href="../index.php" style="border-top-left-radius: 10px;" class="<?php echo $page1;?>">Matches</a></li>
<li><a href="../players.php" class="<?php echo $page2;?>">Players</a></li>
<li><a href="../teams.php" class="<?php echo $page3;?>">Teams</a></li>
<li><a href="../matches/results.php" class="<?php echo $page4;?>">Results</a></li>
<li><a href="../maps.php" class="<?php echo $page5;?>">Maps</a></li>
<li><a href="../events.php" style="border-top-right-radius: 10px;" class="<?php echo $page6;?>">Events</a></li>
</ul>
    </div>

</body>
</html>
