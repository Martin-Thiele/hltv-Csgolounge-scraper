<html>
<head>
<?php 
error_reporting(0);
include_once "navbar.php";
include_once "connection.php";
?>
<link rel="stylesheet" type="text/css" href="css/style.css">
<link href="bootstrap/css/bootstrap.css" rel="stylesheet">
</head>
<body>
<div class="wrap">
<?php
  $rows = 5;
$mid = ($_GET['mid']);
// Get matchinfo


    $stmt = $conn->prepare('SELECT T.cid, name, complete,subcid, subname, logo1, logo2, name1, name2, tcountry1, tcountry2, mid, hltv, csgl, csglodds1, csglodds2, match_time, match_date, tid_1, tid_2 FROM Competitions
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
FROM Matches m WHERE mid = ? LIMIT 1) AS T
ON Competitions.cid = T.cid');
    $stmt->bind_param('s', $mid);
    $stmt->execute();
    $results = $stmt->get_result();
    $data = $results->fetch_assoc();
if(!$data){echo "<center>No match found for the given match id</center>"; return;}

$tid1 = $data['tid_1'];
$tid2 = $data['tid_2'];
// Get playerinfo
$playerquery = "SELECT tid, T.pid as pid, ign, country FROM Players
	            INNER JOIN
				(SELECT * FROM Belongs_to
				WHERE tid = $tid1 OR tid = $tid2) AS T
				ON Players.pid = T.pid LIMIT 10";
$playerfetch = $conn->query($playerquery);
$t1pids = array();
$t1countries = array();
$t1tids = array();
$t1igns = array();
$t2pids = array();
$t2countries = array();
$t2tids = array();
$t2igns = array();
while($player = $playerfetch->fetch_assoc()){
    if($tid1 == $player['tid']){
        $t1pids[] = $player['pid'];
        $t1tids[] = $tid1;
        $t1igns[] = $player['ign'];
        $t1countries[] = $player['country'];
    }
    else{
        $t2pids[] = $player['pid'];
        $t2tids[] = $tid2;
        $t2igns[] = $player['ign'];
        $t2countries[] = $player['country'];
    }
}
?>

<div class="wrapmatch">
    <div class="wrapmatchteam">
        <a id="teamtitle" href="teamid.php?tid=<?php echo $tid1 ?>">
            <?php echo $data['name1'] ?>
        </a><br/>
            <img src="<?php echo $data['logo1'] ?>" 
                 onerror="this.src = 'teamlogos/default.png';" 
                 style="height:150px; width:150px;">
        <div>
        <table class='table' style='border: 0'>
            <?php

            if(!$t1igns){echo "No players found";}
            else{
            for($j = 0; $j < 5; $j++){
                if($t1igns[$j] != ""){
                $flag = str_replace(' ', '', (strtolower($t1countries[$j])));
                echo "<tr>
                <td width='150px'>
                <img src='flags/".$flag.".png' title='".$t1countries[$j]."''> 
                <a href='playerid.php?pid=".$t1pids[$j]."'>
                    ".$t1igns[$j]."
                </a>
                </td></tr>";
            }}}
            if(($data['tid_1'] == 108 && $data['tid_2'] == 108) || (!$data['csglodds1'] && !$data['csglodds2'])){$data['csglodds1'] = ""; $data['csglodds2'] = "";}
            else{$data['csglodds1'] =  $data['csglodds1'] . "%"; $data['csglodds2'] =  $data['csglodds2'] . "%";}
             ?>
         </table>
        </div>
    </div>

    <div id='result'><?php echo  $data['csglodds1'] ?> - <?php echo $data['csglodds2'] ?></div>
    <div class="wrapmatchteam">
    <a id="teamtitle" href="teamid.php?tid=<?php echo $tid2 ?>">
        <?php echo $data['name2'] ?>
    </a><br/>
        <img src="<?php echo $data['logo2'] ?>" 
             onerror="this.src = 'teamlogos/default.png';" 
            style="height:150px; width:150px;">
        <div>
        <table class='table' style='border: 0'>
            <?php

            if(!$t2igns){echo "No players found";}
            else{
            for($j = 0; $j < 5; $j++){
                if($t2igns[$j] != ""){
                $flag = str_replace(' ', '', (strtolower($t2countries[$j])));
                echo "<tr>
                <td width='150px'>
                <img src='flags/".$flag.".png' title='".$t2countries[$j]."''> 
                <a href='playerid.php?pid=".$t2pids[$j]."'>
                    ".$t2igns[$j]."
                </a>
                </td></tr>";
            }}}
             ?>
        </table>
        </div>
    </div>
</div>
<div class='container'>
    <div class='containermaps' style='width:initial'>
<?php

        // Get Matches
$stmt = $conn->prepare('SELECT name, pmapid, TT.mapid, score_1, score_2 FROM Maps
                    INNER JOIN
                    (SELECT T.pmapid, mapid, score_1, score_2 FROM Playedmaps 
                    INNER JOIN 
                    (SELECT mid, pmapid FROM Map_belongs_to WHERE mid = ?) AS T 
                    ON Playedmaps.pmapid = T.pmapid) AS TT
                    ON Maps.mapid = TT.mapid');
        $stmt->bind_param('s', $mid);
        $stmt->execute();
        $results = $stmt->get_result();

        $bo = mysqli_num_rows($results);
        $mapids = array();
        $mapnames = array();
        echo "<h3><center>BO".$bo."</h3></center>";
        if(!$mapfetch){echo mysqli_error($conn);}
        while($map = $results->fetch_assoc()){
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
            <div id = 'midpane'>  <img src='maps/".$map['name'].".png'> </div>
            <div id = '$pane2'>$score2</div>";
            echo "<br/>";

        }
?>

    </div>
</div>
<?php 
echo "<title>BO".$bo.": ".$data['name1']." vs ".$data['name2']."</title>";

$newDate = date("d.m.Y", strtotime($data['match_date']));
echo "<b>Date: </b>".$newDate."<br/>";
echo "<b>Time: </b>".$data['match_time']."<br/>";
echo "<b>Competition: </b><a href='subcomp.php?subcid=".$data['subcid']."'>".$data['subname']."</a><br/>";
if($data['hltv']){
  echo "<b><img src='imgs/hltv.png'> <a href='".$data['hltv']."'>HLTV</a></b><br/>";
}
if($data['csgl']){
  echo "<b><img src='imgs/csgl.png'> <a href='".$data['csgl']."'>Csgolounge</a></b><br/>";
}
if($data['complete'] == 0){
echo "<h1>Recent matches</h1>";
echo "<h3>Head to head </h3>";







// H2H
$h2hfindquery = "SELECT mid, tid_1, tid_2, match_date FROM Matches WHERE complete = 1 AND
((tid_1 = $tid1 AND tid_2 = $tid2) OR 
(tid_2 = $tid1 AND tid_1 = $tid2))
ORDER BY match_date desc, match_time desc LIMIT 5";
$h2hfindfetch = $conn->query($h2hfindquery);
if(!$h2hfindfetch){echo mysqli_error($conn);}
echo "<div class='container2'>";
echo "<div class='wraplatestcenter'>";
echo "<table class='table table-hover'>";
if(mysqli_num_rows($h2hfindfetch) != 0){
    while($h2hfind = $h2hfindfetch->fetch_assoc()){
        $t1win = 0;
        $t2win = 0;

        $h2hquery = "SELECT name as mapname, pmapid, score_1, score_2 FROM Maps
                    INNER JOIN
                    (SELECT T.pmapid, mapid, score_1,score_2 FROM Playedmaps
                    INNER JOIN
                    (SELECT pmapid FROM Map_belongs_to WHERE mid = ".$h2hfind['mid'].") AS T
                    ON Playedmaps.pmapid = T.pmapid) AS TT
                    ON Maps.mapid = TT.mapid";
        $h2hfetch = $conn->query($h2hquery);
        $h2hbo = mysqli_num_rows($h2hfetch);
        while($h2h = $h2hfetch->fetch_assoc()){
                        if($h2hbo == 1){
                $map = $h2h['mapname'];
                $t1win = $h2h['score_1'];
                $t2win = $h2h['score_2'];
            }
            else{
                if($h2h['score_1'] > $h2h['score_2'] && ($h2h['score_1'] > 15 || strcmp($h2h['mapname'], "Unplayed"))){
                $t1win++;
                }
                if($h2h['score_1'] < $h2h['score_2'] && ($h2h['score_2'] > 15 || strcmp($h2h['mapname'], "Unplayed"))){
                $t2win++;
                }
            }
        }
        if($h2hfind['tid_1'] == $tid1){
            if($h2hbo > 1){
                $map = "BO" . $h2hbo;
            }
            $team1 = $data['name1'];
            $team2 = $data['name2'];
            $score1 = $t1win;
            $score2 = $t2win;
            $teamflag = str_replace(' ', '', (strtolower($data['tcountry1'])));
            $enemy = $h2hfind['name2'];
        }
        else{
            if($h2hbo > 1){
                $map = "BO" . $h2hbo;
            }
            $team1 = $data['name1'];
            $team2 = $data['name2'];
            $score1 = $t2win;
            $score2 = $t1win;
            $teamflag2 = str_replace(' ', '', (strtolower($data['tcountry2'])));
            $enemy = $h2hfind['name1'];
        }
        $newDate = date("d.m", strtotime($h2hfind['match_date']));
        if($score1 > $score2){$winloss = "win"; $winloss2 = "loss";}
        else if($score2 > $score1){$winloss = "loss"; $winloss2 = "win";}
        else {$winloss = "draw"; $winloss2 = "draw";}
        $teamflag = str_replace(' ', '', (strtolower($data['tcountry1'])));
        $teamflag2 = str_replace(' ', '', (strtolower($data['tcountry2'])));

        echo "<tr>
              <td>".$newDate."</td>
              <td>".$map."</td>
              <td><img src='flags/".$teamflag.".png''> 
              <a href='matchid.php?mid=".$h2hfind['mid']."'>".$team1."</a></td>
              <td class='".$winloss."'>".$score1."</td>
              <td class='center'>-</td>
              <td class='".$winloss2."'>".$score2."</td>
              <td><img src='flags/".$teamflag2.".png''> 
              <a href='matchid.php?mid=".$h2hfind['mid']."'>".$team2."</a></td></tr>";

    }
}
else{echo "No Head to head matches found";}
echo "</table></div>";


// Recent matches
echo "<h3>Recent matches</h3>";

$wraprecentleftquery = "SELECT  (SELECT n.name 
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
        mid, tid_1, tid_2, match_date
FROM Matches m WHERE complete = 1 AND
(tid_1 = $tid1 OR 
tid_2 = $tid1)
ORDER BY match_date desc, match_time desc LIMIT 5";
$wraprecentleftfetch = $conn->query($wraprecentleftquery);
if(!$wraprecentleftfetch){echo mysqli_error($conn);}

echo "<div class='wraplatestleft'>";
echo "<table class='table table-hover'>";
if(mysqli_num_rows($wraprecentleftfetch) != 0){
    while($wraprecentleft = $wraprecentleftfetch->fetch_assoc()){
        $t1win = 0;
        $t2win = 0;

        $wrapleftquery = "SELECT name as mapname, pmapid, score_1, score_2 FROM Maps
                    INNER JOIN
                    (SELECT T.pmapid, mapid, score_1,score_2 FROM Playedmaps
                    INNER JOIN
                    (SELECT pmapid FROM Map_belongs_to WHERE mid = ".$wraprecentleft['mid'].") AS T
                    ON Playedmaps.pmapid = T.pmapid) AS TT
                    ON Maps.mapid = TT.mapid";
        $wrapleftfetch = $conn->query($wrapleftquery);
        $wrapleftbo = mysqli_num_rows($wrapleftfetch);
        while($wrapleft = $wrapleftfetch->fetch_assoc()){
            if($wrapleftbo == 1){
                $map = $wrapleft['mapname'];
                $t1win = $wrapleft['score_1'];
                $t2win = $wrapleft['score_2'];
            }
            else{
                if($wrapleft['score_1'] > $wrapleft['score_2'] && ($wrapleft['score_1'] > 15 || $wraprecentright['mapname'] == "Unplayed")){
                $t1win++;
                }
                if($wrapleft['score_1'] < $wrapleft['score_2'] && ($wrapleft['score_2'] > 15 || $wraprecentright['mapname'] == "Unplayed")){
                $t2win++;
                }
            }
        }
        if($wraprecentleft['tid_1'] == $tid1){
            if($wrapleftbo > 1){
                $map = "BO" . $wrapleftbo;
            }
            $score1 = $t1win;
            $score2 = $t2win;
            $teamflag = str_replace(' ', '', (strtolower($wraprecentleft['tcountry2'])));
            $enemy = $wraprecentleft['name2'];
        }
        else{
            if($wrapleftbo > 1){
                $map = "BO" . $wrapleftbo;
            }
            $score1 = $t2win;
            $score2 = $t1win;
            $teamflag = str_replace(' ', '', (strtolower($wraprecentleft['tcountry1'])));
            $enemy = $wraprecentleft['name1'];
        }


        $newDate = date("d.m", strtotime($wraprecentleft['match_date']));
        if($score1 > $score2){$winloss = "win"; $winloss2 = "loss";}
        else if($score2 > $score1){$winloss = "loss"; $winloss2 = "win";}
        else {$winloss = "draw"; $winloss2 = "draw";}


        echo "<tr>
              <td>".$newDate."</td>
              <td>".$map."</td>
              <td class='".$winloss."'>".$score1."</td>
              <td class='center'>-</td>
              <td class='".$winloss2."'>".$score2."</td>
              <td><img src='flags/".$teamflag.".png''> 
              <a href='matchid.php?mid=".$wraprecentleft['mid']."'>".$enemy."</a></td></tr>";

    }
}
else{echo "No recent matches found for ".$data['name1'];}
echo "</table></div>";

$wraprecentrightfindquery = "SELECT  (SELECT n.name 
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
        mid, tid_1, tid_2, match_date
FROM Matches m WHERE complete = 1 AND
(tid_1 = $tid2 OR 
tid_2 = $tid2)
ORDER BY match_date desc, match_time desc LIMIT 5";
$wraprecentrightfindfetch = $conn->query($wraprecentrightfindquery);
if(!$wraprecentrightfindfetch){echo mysqli_error($conn);}

echo "<div class='wraplatestright'>";
echo "<table class='table table-hover'>";
if(mysqli_num_rows($wraprecentrightfindfetch) != 0){
    while($wraprecentrightfind = $wraprecentrightfindfetch->fetch_assoc()){
        $t1win = 0;
        $t2win = 0;

        $wraprecentrightquery = "SELECT name as mapname, pmapid, score_1, score_2 FROM Maps
                    INNER JOIN
                    (SELECT T.pmapid, mapid, score_1,score_2 FROM Playedmaps
                    INNER JOIN
                    (SELECT pmapid FROM Map_belongs_to WHERE mid = ".$wraprecentrightfind['mid'].") AS T
                    ON Playedmaps.pmapid = T.pmapid) AS TT
                    ON Maps.mapid = TT.mapid";
        $wraprecentrightfetch = $conn->query($wraprecentrightquery);
        $wraprecentrightbo = mysqli_num_rows($wraprecentrightfetch);
        while($wraprecentright = $wraprecentrightfetch->fetch_assoc()){
            if($wraprecentrightbo == 1){
                $map = $wraprecentright['mapname'];
                $t1win = $wraprecentright['score_1'];
                $t2win = $wraprecentright['score_2'];
            }
            else{
                if($wraprecentright['score_1'] > $wraprecentright['score_2'] && ($wraprecentright['score_1'] > 15 || $wraprecentright['mapname'] == "Unplayed")){
                $t1win++;
                }
                if($wraprecentright['score_1'] < $wraprecentright['score_2'] && ($wraprecentright['score_2'] > 15 || $wraprecentright['mapname'] == "Unplayed")){
                $t2win++;
                }
            }
        }
        if($wraprecentrightfind['tid_1'] == $tid2){
            if($wraprecentrightbo > 1){
                $map = "BO" . $wraprecentrightbo;
            }
            $score1 = $t1win;
            $score2 = $t2win;
            $teamflag = str_replace(' ', '', (strtolower($wraprecentrightfind['tcountry2'])));
            $enemy = $wraprecentrightfind['name2'];
        }
        else{
            if($wraprecentrightbo > 1){
                $map = "BO" . $wraprecentrightbo;
            }
            $score1 = $t2win;
            $score2 = $t1win;
            $teamflag = str_replace(' ', '', (strtolower($wraprecentrightfind['tcountry1'])));
            $enemy = $wraprecentrightfind['name1'];
        }


        $newDate = date("d.m", strtotime($wraprecentrightfind['match_date']));
        if($score1 > $score2){$winloss = "win"; $winloss2 = "loss";}
        else if($score2 > $score1){$winloss = "loss"; $winloss2 = "win";}
        else {$winloss = "draw"; $winloss2 = "draw";}


        echo "<tr>
              <td>".$newDate."</td>
              <td>".$map."</td>
              <td class='".$winloss."'>".$score1."</td>
              <td class='center'>-</td>
              <td class='".$winloss2."'>".$score2."</td>
              <td><img src='flags/".$teamflag.".png''> 
              <a href='matchid.php?mid=".$wraprecentrightfind['mid']."'>".$enemy."</a></td></tr>";

    }
}
else{echo "No recent matches found for ".$data['name2'];}
echo "</table></div>";

// Wrap recent maps

for($i = 0; $i < $bo; $i++){
    if($mapnames[$i] != "Unplayed" AND $mapnames[$i] != "TBA"){
echo "<div style='clear:both;'>";
echo "<h3 style='padding-top: 15px;'>Recent matches on ".$mapnames[$i]."</h3>";
echo "<div class='wraplatestleft'>";
echo "<table class='table table-hover'>";
$wrapleftmapfindquery = "SELECT TT.pmapid, mid,tid_1, tid_2, name1, name2, score_1, score_2, mapid, match_date, tcountry1, tcountry2 FROM Playedmaps
INNER JOIN
(SELECT pmapid, T.mid, name1, name2, match_date, tcountry1, tcountry2, tid_1, tid_2 FROM Map_belongs_to
INNER JOIN
(SELECT  (SELECT n.name 
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
        mid, tid_1, tid_2, match_date
FROM Matches m WHERE complete = 1 AND
(tid_1 = $tid1 OR 
tid_2 = $tid1)
ORDER BY match_date desc, match_time desc) AS T
ON T.mid = Map_belongs_to.mid) AS TT
ON TT.pmapid = Playedmaps.pmapid WHERE mapid = ".$mapids[$i]." AND (score_1 > 15 OR score_2 > 15) LIMIT 5";
$wrapleftmapfindfetch = $conn->query($wrapleftmapfindquery);
if(!$wrapleftmapfindfetch){echo mysqli_error($conn);}
while($wrapleftmap = $wrapleftmapfindfetch->fetch_assoc()){
    if($wrapleftmap['tid_1'] == $tid1){
        $score1 = $wrapleftmap['score_1'];
        $score2 = $wrapleftmap['score_2'];
        $teamflag = str_replace(' ', '', (strtolower($wrapleftmap['tcountry2'])));
        $enemy = $wrapleftmap['name2'];
    }
    else{
        $score1 = $wrapleftmap['score_2'];
        $score2 = $wrapleftmap['score_1'];
        $teamflag = str_replace(' ', '', (strtolower($wrapleftmap['tcountry1'])));
        $enemy = $wrapleftmap['name1'];
    }
    $newDate = date("d.m", strtotime($wrapleftmap['match_date']));
    if($score1 > $score2){$winloss = "win"; $winloss2 = "loss";}
    else if($score2 > $score1){$winloss = "loss"; $winloss2 = "win";}
    else {$winloss = "draw"; $winloss2 = "draw";}

            echo "<tr>
              <td>".$newDate."</td>
              <td>".$mapnames[$i]."</td>
              <td class='".$winloss."'>".$score1."</td>
              <td class='center'>-</td>
              <td class='".$winloss2."'>".$score2."</td>
              <td><img src='flags/".$teamflag.".png''> 
              <a href='matchid.php?mid=".$wrapleftmap['mid']."'>".$enemy."</a></td></tr>";

} // End while loop


echo "</table></div>";

echo "<div class='wraplatestright'>";
echo "<table class='table table-hover'>";
$wraprightmapfindquery = "SELECT TT.pmapid, mid,tid_1, tid_2, name1, name2, score_1, score_2, mapid, match_date, tcountry1, tcountry2 FROM Playedmaps
INNER JOIN
(SELECT pmapid, T.mid, name1, name2, match_date, tcountry1, tcountry2, tid_1, tid_2 FROM Map_belongs_to
INNER JOIN
(SELECT  (SELECT n.name 
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
        mid, tid_1, tid_2, match_date
FROM Matches m WHERE complete = 1 AND
(tid_1 = $tid2 OR 
tid_2 = $tid2)
ORDER BY match_date desc, match_time desc) AS T
ON T.mid = Map_belongs_to.mid) AS TT
ON TT.pmapid = Playedmaps.pmapid WHERE mapid = ".$mapids[$i]." AND (score_1 > 15 OR score_2 > 15) LIMIT 5";
$wraprightmapfindfetch = $conn->query($wraprightmapfindquery);
if(!$wraprightmapfindfetch){echo mysqli_error($conn);}
while($wraprightmap = $wraprightmapfindfetch->fetch_assoc()){
    if($wraprightmap['tid_1'] == $tid2){
        $score1 = $wraprightmap['score_1'];
        $score2 = $wraprightmap['score_2'];
        $teamflag = str_replace(' ', '', (strtolower($wraprightmap['tcountry2'])));
        $enemy = $wraprightmap['name2'];
    }
    else{
        $score1 = $wraprightmap['score_2'];
        $score2 = $wraprightmap['score_1'];
        $teamflag = str_replace(' ', '', (strtolower($wraprightmap['tcountry1'])));
        $enemy = $wraprightmap['name1'];
    }
    $newDate = date("d.m", strtotime($wraprightmap['match_date']));
    if($score1 > $score2){$winloss = "win"; $winloss2 = "loss";}
    else if($score2 > $score1){$winloss = "loss"; $winloss2 = "win";}
    else {$winloss = "draw"; $winloss2 = "draw";}

            echo "<tr>
              <td>".$newDate."</td>
              <td>".$mapnames[$i]."</td>
              <td class='".$winloss."'>".$score1."</td>
              <td class='center'>-</td>
              <td class='".$winloss2."'>".$score2."</td>
              <td><img src='flags/".$teamflag.".png''> 
              <a href='matchid.php?mid=".$wraprightmap['mid']."'>".$enemy."</a></td></tr>";

} // End while loop


echo "</table></div>";


echo "</div>";
}
} // End for loop

}
?>
</div>
</div>
<?php include_once("footer.php") ?>
</body>
</html>