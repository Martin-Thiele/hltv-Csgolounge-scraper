<html>
<head>

<?php
include_once "navbar.php";
?>
<link rel="stylesheet" type="text/css" href="css/style.css">
<link href="bootstrap/css/bootstrap.css" rel="stylesheet">
<title>About</title>
<meta name="description" content="About the project" />

</head>
<body> 
<div class="wrap">
<h1 style="margin-bottom: -20px;">About</h1>
<h3 style="padding-top: -60px; margin-left:40px; padding: 0px;">Early stage</h3>
<p style="color: #444;"><b style="padding: 0px;">
    Brintos.dk/csgo</b> is a website designed to help you predict winners of CS:GO matches,
    it originally started back in february 2015, as a project for myself to figure out the best
    current teams, and their shape for as to how i should bet my skins on 
<a href="http://www.csgolounge.com">csgolounge</a>. I now check the matchpages before all of my bets.
</p>
<h3 style="margin-left:40px; padding: 0px;">How it works</h3>
<p style="color: #444;">
    The website keeps track of the majority of CS:GO matches. As of July 2015, mainly by scraping 
    <a href="http://www.hltv.org"><br/>hltv</a> (Special thanks to them). 
    Then the website finds the latest results for the teams playing, and displays their data on a matchpage. 
    As a bonus, odds from csgolounge will be also be displayed.
</p>
<h3 style="padding-top: 0px; margin-left:40px; padding: 0px;">Last words, and a heads-up</h3>
<p style="color: #444;">
    The data on the website only updates, when the bot is running, 
    and when the bot is running it updates every 10th minute. 
    This is to prevent ddosing csgolounge and hltv, and avoid using too much bandwith.
</p>
</div>

<?php include_once("footer.php"); ?>
</body>

</html>