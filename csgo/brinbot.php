<html>
<head>

<?php
include_once "navbar.php";
?>
<link rel="stylesheet" type="text/css" href="css/style.css">
<link href="bootstrap/css/bootstrap.css" rel="stylesheet">
<title>Brinbot</title>
</head>
<body> 
<div class="wrap">
<h1 style="margin-bottom: -20px;">Brinbot</h1>
<i style="margin-left: 30px;">Now on <a href="https://github.com/Shrewbi/hltv-Csgolounge-scraper">github!</a></i>
<p style="color: #444;">
    Brinbot is a scraping bot written in java with the purpose of fetching 
    new matches and updates from hltv, and odds from csgolounge.
    It is optimised to scrape as little as possible and only the necessary data.
<br/><br/>
    <b style="padding: 0px;">hltv Settings:</b>
    <br/>
    <b style="padding: 0px;">scrapetimer: </b> 10 minutes<br/>
    <b style="padding: 0px;">requests per 10th minute:</b> (x + 1). Where x is the amount of active matches
    <br/><br/>
    <b style="padding: 0px;">csgl Settings:</b>
    <br/>
    <b style="padding: 0px;">scrapetimer: </b> 10 minutes<br/>
    <b style="padding: 0px;">requests per 10th minute:</b> 1
    <br/><br/>

    If you are the owner of either csgolounge, or hltv and have any issue regarding this bot. Please
    contact me for as to how i can help.
</div>

<?php include_once("footer.php"); ?>
</body>

</html>