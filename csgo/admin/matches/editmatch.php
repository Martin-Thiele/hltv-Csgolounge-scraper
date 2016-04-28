<html>
<head>
<?php
include_once "../navbar.php";
include_once "../connection.php";
$mid = ($_GET['mid']);
?>
        <link href="<?php echo "//" . $url . "/css/insertmatch.css"; ?>" rel="stylesheet">
        <link href="<?php echo "//" . $url . "/css/style.css"; ?>" rel="stylesheet">
        <link href="<?php echo "//" . $url . "/bootstrap/css/bootstrap.css"; ?>" rel="stylesheet">
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        <script src="<?php echo "//" . $url . "/typeahead/typeahead.min.js" ?>"></script>
        <script src="<?php echo "//" . $url . "/typeahead/typeahead.js" ?>"></script>
</head>
<body>
<div class="wrap">
<?php

if(isset($_POST['submit']))
{


$name1 = $conn->real_escape_string($_POST['team1']);
$tid1 = "SELECT tid FROM Teams WHERE name = '$name1'";
$retval = $conn->query($tid1);
$tid1 = $retval->fetch_assoc();

$name2 = $conn->real_escape_string($_POST['team2']);
$tid2 = "SELECT tid FROM Teams WHERE name = '$name2'";
$retval = $conn->query($tid2);
$tid2 = $retval->fetch_assoc();

$compname = $conn->real_escape_string($_POST['comp']);
$subcid = "SELECT subcid FROM Subcomp WHERE name = '$compname'";
$retval = $conn->query($subcid);
$comp = $retval->fetch_assoc();

	$sql = "UPDATE Matches
			SET tid_1       =  '".$tid1['tid']."',
				tid_2       =  '".$tid2['tid']."',
				match_date  =  '".$_POST['date']."',
				match_time  =  '".$_POST['time']."',
				subcid      =  '".$comp['subcid']."',
				csgl        =  '".$_POST['csgl']."',
				hltv        =  '".$_POST['hltv']."',
				csglodds1   =  '".$_POST['odds1']."',
				csglodds2   =  '".$_POST['odds2']."',
				complete    =  '".$_POST['complete']."'
			WHERE mid       =  '$mid'" ;


	$retval = $conn->query($sql);
	if(!$retval )
		{
		  die('Could not update data: ' . mysqli_error($conn));
		}
	else{
		echo "<center>Updated</center>";
	}
}
$dataquery = "SELECT T.cid, subcid, name, complete, subname, logo1, logo2, name1, name2, tcountry1, tcountry2, mid, hltv, csgl, csglodds1, csglodds2, match_time, match_date, tid_1, tid_2 FROM Competitions
INNER JOIN
(SELECT  (SELECT n.logo
        FROM   Teams n 
        WHERE  m.tid_1 = n.tid) logo1,
        (SELECT n.logo
        FROM   Teams n 
        WHERE  m.tid_2 = n.tid) logo2,
       (SELECT n.name 
        FROM   Teams n 
        WHERE  m.tid_1 = n.tid) name1,
        (SELECT n.name 
        FROM   Teams n 
        WHERE  m.tid_2 = n.tid) name2,
       (SELECT n.tcountry 
        FROM   Teams n 
        WHERE  m.tid_1 = n.tid) tcountry1,
        (SELECT n.tcountry 
        FROM   Teams n 
        WHERE  m.tid_2 = n.tid) tcountry2,
        (SELECT n.name
        FROM   Subcomp n
        WHERE  m.subcid = n.subcid) subname,
        (SELECT n.cid
        FROM   Comp_belongs_to n 
        WHERE  m.subcid = n.subcid) cid
        , mid, hltv, csglodds1, csglodds2, match_time, match_date, tid_1, tid_2, subcid, csgl, complete
FROM Matches m WHERE mid = $mid LIMIT 1) AS T
ON Competitions.cid = T.cid";
$datafetch = $conn->query($dataquery);
if(!$datafetch){echo mysqli_error($conn);}
$data = $datafetch->fetch_assoc();
$tid1 = $data['tid_1'];
$tid2 = $data['tid_2'];

?>
<form action="<?php echo "editmatch.php?mid=$mid"?>" method="post">
<div class="wrapmatch">
    <div class="wrapmatchteam">
        <input class="typeahead" type="text" name="team1" value="<?php echo $data['name1']?>" autocomplete="off" /><br/>
            <img src="<?php echo "../../".$data['logo1'] ?>" 
                 onerror="this.src = '../../teamlogos/default.png';" 
                 style="height:150px; width:150px;">
        <div>
        <table class='table' style='border: 0'>
            <?php
             ?>
         </table>
        </div>
    </div>

    <div id='result'><input style="width:40px; text-align:center;" class="input" type="text" name="odds1" value="<?php echo $data['csglodds1']?>" autocomplete="off" /> 
    <b style="vertical-align:middle;">-</b>
    <input class="input" style="width:40px; text-align:center;" type="text" name="odds2" value="<?php echo $data['csglodds2']?>" autocomplete="off" /></div>
    <div class="wrapmatchteam">
    <input class="typeahead" type="text" name="team2" value="<?php echo $data['name2']?>" autocomplete="off" /><br/><br/>
        <img src="<?php echo "../../".$data['logo2'] ?>" 
             onerror="this.src = '../../teamlogos/default.png';" 
            style="height:150px; width:150px;">
        <div>
        <table class='table' style='border: 0'>
        </table>
        </div>
    </div>
</div>
<div class='container'>
    <div class='containermaps' style='width:initial'>
<?php

        // Get Matches
        $mapquery = "SELECT name, pmapid, TT.mapid, score_1, score_2 FROM Maps
                    INNER JOIN
                    (SELECT T.pmapid, mapid, score_1, score_2 FROM Playedmaps 
                    INNER JOIN 
                    (SELECT mid, pmapid FROM Map_belongs_to WHERE mid =$mid) AS T 
                    ON Playedmaps.pmapid = T.pmapid) AS TT
                    ON Maps.mapid = TT.mapid
                        ";
        $mapfetch = $conn->query($mapquery);
        $bo = mysqli_num_rows($mapfetch);

        $mapids = array();
        $mapnames = array();
        echo "<h3><center>BO".$bo."</h3></center>";
        if(!$mapfetch){echo mysqli_error($conn);}
        while($map = $mapfetch->fetch_assoc()){
            $score1 = '&nbsp;';
            $score2 = '&nbsp;';
            $pane1 = 'scorepane';
            $pane2 = 'scorepane';
            $mapids[] = $map['mapid'];
            $mapnames[] = $map['name'];
            if($map['score_1'] OR $map['score_2']) {$score1 = $map['score_1']; $score2 = $map['score_2'];}
            if($score1 > $score2){$pane1 = 'winnerpane'; $pane2 = 'loserpane'; $t1win++; $completecount++;}
            if($score2 > $score1){$pane1 = 'loserpane'; $pane2 = 'winnerpane'; $t2win++; $completecount++;}
            if($map['mapid'] == 0){$map['name'] = 'TBA';}
            
            echo "
            <div id = '$pane1'>$score1</div>
            <div id = 'midpane'>  <img src='../../maps/".$map['name'].".png'> </div>
            <div id = '$pane2'>$score2</div>";
            echo "<br/>";

        }
?>

    </div>
</div>

<title>BO<?php echo "$bo: ".$data['name1']." vs ".$data['name2'].""?></title>
<br/>
<div style="vertical-align:middle"><b>date: </b> <input class="input" type="text" name="date" value="<?php echo $data['match_date']?>" autocomplete="off" /></div>
<div style="vertical-align:middle"><b>time: </b> <input class="input" type="text" name="time" value="<?php echo $data['match_time']?>" autocomplete="off" /></div>
<div style="vertical-align:middle"><b>competition: </b> <input class="typeahead2" type="text" name="comp" value="<?php echo $data['subname']?>" autocomplete="off" /></div>
<div style="vertical-align:middle"><b>csgl: </b> <input class="input" type="text" name="csgl" value="<?php echo $data['csgl']?>" autocomplete="off" /></div>
<div style="vertical-align:middle"><b>hltv: </b> <input class="input" type="text" name="hltv" value="<?php echo $data['hltv']?>" autocomplete="off" /></div>
<div style="vertical-align:middle"><b>complete: </b> <input class="input" type="text" name="complete" value="<?php echo $data['complete']?>" autocomplete="off" /></div>
<input class="input" type="hidden" name="subcid" value="<?php echo $data['subcid']?>" autocomplete="off" />
<center><input class="submit" style="width:300px" type="submit" name="submit" value="Submit" /></center>
</div>
</body>
</html>