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
<div class="wrap" style="min-height: 300px">
    <div class="container2">
        <div class="wrapleft">
        <?php
        $tid = ($_GET['tid']);

        $stmt = $conn->prepare('SELECT name, tid, tcountry, logo, active from Teams WHERE tid= ?');
        $stmt->bind_param('s', $tid);
        $stmt->execute();
        $results = $stmt->get_result();
        $teaminfo = $results->fetch_assoc();
        if(!$teaminfo){echo "<center>No team found for the given team id</center>"; return;}
        ?>

        <title> <?php echo $teaminfo['name']; ?></title>

        <!-- Teamlogo pane //-->
        <img style="vertical-align:top; height:300px; width:300px" 
        src="<?php echo $teaminfo['logo'] ?>"
        onerror="this.src = 'teamlogos/default.png';"> 
        </div> 

        <?php

        //Information pane
        $flag = str_replace(' ', '', (strtolower($teaminfo["tcountry"])));
        echo "
        <div class='wrapright2'>
        <center>
        <h1>".$teaminfo['name']. "</h1>
        </center>" ;
        if($teaminfo['active'] == 1){$active = "yes";}
        else{$active = "no";}
        echo "
            <div class='wrapright' style='height: 200px;'>
                <div style='width: 275px; margin: 0 auto;'>
                <h2>Information</h2>
                <b>Active:</b> ".$active. "<br/>
                <b>Country:</b> <img src=flags/".$flag. ".png> " .$teaminfo['tcountry']. "

            </div>
        </div>";
        ?>




        <!-- Roster pane //-->
        <div class="wrapright" style="height: 200px; width:275px">
            <h2>Roster</h2>
            <div style="text-align: center">
                <div class="table" style="max-width: 275px; margin: 0 auto;">
                <?php


        $stmt = $conn->prepare('SELECT Players.pid, ign, country, image, name, tcountry, logo FROM Players
                INNER JOIN
                (SELECT pid, T.tid, name, tcountry, logo FROM Belongs_to
                INNER JOIN
                (SELECT name, tid, tcountry, logo from Teams WHERE tid = ?) AS T ON Belongs_to.tid = T.tid AND T.tid != 40) AS TA ON TA.pid = Players.pid LIMIT 5');
        $stmt->bind_param('s', $tid);
        $stmt->execute();
        $results = $stmt->get_result();
                if (mysqli_num_rows($results) > 0) {
                    echo "<table class='table'>";
                    while($row = $results->fetch_assoc()) {
                        $flag = str_replace(' ', '', (strtolower($row["country"])));
                        echo "<tr><td width='600px'' height='20px'><img src='flags/".$flag.".png' title='".$row["country"]."''> <a href='playerid.php?pid=".$row["pid"]."'>".$row["ign"]."</td></a>";
                    }
                    echo "</table>";
                } else {
                    echo "This team does not have any players";
                }
                ?>
                </div>
            </div>
        </div>
    </div>



            <div class="wrapright" style="text-align: center; width: 260px; margin-right: 12px; margin-left: 12px;">
        <h2>Playertransfers</h2>
        <div class="table table-hover" style="width: 260px; margin: 0 auto;">
                <?php
        $stmt = $conn->prepare('SELECT T.pid, ign, country, tid1, tid2, transdate FROM Players
                        INNER JOIN
                        (SELECT * FROM Playertransfers WHERE tid1 = ? OR tid2 = ? ORDER BY transdate desc) AS T
                        ON T.pid = Players.pid LIMIT 11');
        $stmt->bind_param('ss', $tid, $tid);
        $stmt->execute();
        $results = $stmt->get_result();

                    echo "<table class='table'>";
                    while($row = $results->fetch_assoc()) {
                    	$temp = "joins";
                    	if($row['tid1'] == $tid){$temp = "leaves";}
                        $flag = str_replace(' ', '', (strtolower($row["country"])));
                        $newDate = date("d.m.y", strtotime($row['transdate']));
                        echo "<tr><td width='300px'' height='20px'>".$newDate." <img src='flags/".$flag.".png' title='".$row["country"]."''> <a href='playerid.php?pid=".$row["pid"]."'>".$row["ign"]."</a> ".$temp."</td>";
                    }
                    echo "</table>";
                 
                ?>
            </div>
            </div>





        <!-- Recent matches pane //-->
<?php

        $stmt = $conn->prepare('SELECT  (SELECT n.name 
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
(tid_1 = ? OR 
tid_2 = ?)
ORDER BY match_date desc, match_time desc LIMIT 11');
        $stmt->bind_param('ss', $tid, $tid);
        $stmt->execute();
        $results = $stmt->get_result();

echo "<div class='wrapright' style='text-align: center; width: 360px; margin-right: 12px;'>";
echo "<h2>Recent matches</h2>";
echo "<div class='table table-hover' style='width: 360px; overflow:hidden; margin: 0 auto'>";
echo "<table class='table table-hover'>";
if(mysqli_num_rows($results) != 0){
    while($recent = $results->fetch_assoc()){
        $t1win = 0;
        $t2win = 0;

        $wrapquery = "SELECT name as mapname, pmapid, score_1, score_2 FROM Maps
                    INNER JOIN
                    (SELECT T.pmapid, mapid, score_1,score_2 FROM Playedmaps
                    INNER JOIN
                    (SELECT pmapid FROM Map_belongs_to WHERE mid = ".$recent['mid'].") AS T
                    ON Playedmaps.pmapid = T.pmapid) AS TT
                    ON Maps.mapid = TT.mapid";
        $wrapfetch = $conn->query($wrapquery);
        $wrapbo = mysqli_num_rows($wrapfetch);
        while($wrap = $wrapfetch->fetch_assoc()){
            if($wrapbo == 1){
                $map = $wrap['mapname'];
                $t1win = $wrap['score_1'];
                $t2win = $wrap['score_2'];
            }
            else{
                if($wrap['score_1'] > $wrap['score_2'] && ($wrap['score_1'] > 15 || $wrap['mapname'] == "Unplayed")){
                $t1win++;
                }
                if($wrap['score_1'] < $wrap['score_2'] && ($wrap['score_2'] > 15 || $wrap['mapname'] == "Unplayed")){
                $t2win++;
                }
            }
        }
        if($recent['tid_1'] == $tid){
            if($wrapbo > 1){
                $map = "BO" . $wrapbo;
            }
            $score1 = $t1win;
            $score2 = $t2win;
            $teamflag = str_replace(' ', '', (strtolower($recent['tcountry2'])));
            $enemy = $recent['name2'];
            $tmp1 = $recent['name2'];
            $tmp2 = $recent['tcountry2'];
        }
        else{
            if($wrapbo > 1){
                $map = "BO" . $wrapbo;
            }
            $score1 = $t2win;
            $score2 = $t1win;
            $teamflag = str_replace(' ', '', (strtolower($recent['tcountry1'])));
            $enemy = $recent['name1'];
            $tmp1 = $recent['name1'];
            $tmp2 = $recent['tcountry1'];
        }
            if(strlen($enemy) > 12){$tmp = $enemy; $enemy = substr($enemy, 0, 9) . ".." ;}

        $newDate = date("d.m", strtotime($recent['match_date']));
        if($score1 > $score2){$winloss = "win"; $winloss2 = "loss";}
        else if($score2 > $score1){$winloss = "loss"; $winloss2 = "win";}
        else {$winloss = "draw"; $winloss2 = "draw";}


        echo "<tr>
              <td>".$newDate."</td>
              <td>".$map."</td>
              <td class='".$winloss."'>".$score1."</td>
              <td class='center'>-</td>
              <td class='".$winloss2."'>".$score2."</td>
              <td><img Title = \"". $tmp2 . "\" src='flags/".$teamflag.".png''> 
              <a href='matchid.php?mid=".$recent['mid']."' Title = \"". $tmp1 . "\">".$enemy."</a></td></tr>";

    }
}
else{echo "No recent matches found for ".$data['name1'];}
echo "</table></div></div>";


?>

        <!-- Winrate pane //-->
        <div class="wrapleft" style="width: 230px;"><h2>Winrates</h2><center>
            <div class="table" style="width: 230px;">
            <table class='table'>
            <?php


            // NOT DONE : Calculate matches winrate
            /*
            $sql = "SELECT mid, score_1, score_2 FROM Playedmaps
					INNER JOIN
					(SELECT pmapid, T.mid FROM Map_belongs_to
					INNER JOIN
					(SELECT mid FROM Matches WHERE tid_1 = 1 OR tid_2 = 1) AS T
					ON T.mid = Map_belongs_to.mid) AS TT
					ON TT.pmapid = Playedmaps.pmapid"
			$result = $conn->query($sql2);
            $matcheswon = $result2->fetch_assoc();

            $matchwon = 0;
            $matchlost = 0;
            $mapwon = 0;
            $maploss = 0;
            $bo;
            $save;
            while($matcheswon = $result2->fetch_assoc()) {
            	if($save == $matcheswon['mid']){

            	}
            	$save = $matcheswon['mid'];
            }
			

            // Get amount of matches won
            echo "<tr><td width='80px;'><b style='padding: 0px;'>Matches:</b></td>
            <td>5/7</td></tr>";
			*/

        $stmt = $conn->prepare('SELECT COUNT(*) as totalwins FROM Playedmaps 
                     INNER JOIN (SELECT T.mid, tid_1, tid_2, pmapid FROM Matches
                     INNER JOIN (SELECT * FROM Map_belongs_to) AS T
                     ON Matches.mid = T.mid) AS TT ON TT.pmapid = Playedmaps.pmapid WHERE ((tid_1 = ? AND score_1 > score_2 AND score_1 > 14) OR (tid_2 = ? AND score_2 > score_1 AND score_2 > 14))');
        $stmt->bind_param('ss', $tid, $tid);
        $stmt->execute();
        $results = $stmt->get_result();
        $mapswon = $results->fetch_assoc();


        $stmt = $conn->prepare('SELECT COUNT(*) as totalloss FROM Playedmaps 
                     INNER JOIN (SELECT T.mid, tid_1, tid_2, pmapid FROM Matches
                     INNER JOIN (SELECT * FROM Map_belongs_to) AS T
                     ON Matches.mid = T.mid) AS TT ON TT.pmapid = Playedmaps.pmapid WHERE ((tid_1 = ? AND score_1 < score_2 AND score_2 > 14) OR (tid_2 = ? AND score_2 < score_1 AND score_1 > 14))');
        $stmt->bind_param('ss', $tid, $tid);
        $stmt->execute();
        $results = $stmt->get_result();
        $mapslost= $results->fetch_assoc();


            $mapsplayed = $mapswon['totalwins']+$mapslost['totalloss'];
            $hund = 100;
            if($mapsplayed == 0) {$div = 0;}
            else{$div = round($mapswon['totalwins']/$mapsplayed*$hund);}

            echo "

            <td width='80px;'><b style='padding: 0px;'>Maps:</b></td>
            <td><font color='#2DBB00'>" . $mapswon['totalwins']. "</font> / <font color ='red'>". $mapslost['totalloss']. "</font> (".$div."%)</td></tr></tr>";


            // Get all maps
            $getmaps = "SELECT * FROM Maps WHERE mapid != 9 AND mapid != 10";
            $mapresult = $conn->query($getmaps);

            // Get winrates for all maps
            while($getallmaps = $mapresult->fetch_assoc()) {

            // Amount of times map has been played

            // Amount of times map has been won
            $stmt = $conn->prepare('SELECT COUNT(*) as mapwon FROM Playedmaps
                                    INNER JOIN
                                    (SELECT T.mid, tid_1, tid_2, pmapid FROM Matches
                                    INNER JOIN
                                    (SELECT * FROM Map_belongs_to) AS T
                                    ON Matches.mid = T.mid) AS TT
                                    ON TT.pmapid = Playedmaps.pmapid WHERE 
                                    ((tid_1 = ? AND score_1 > score_2 AND score_1 > 14) OR 
                                    (tid_2 = ? AND score_2 > score_1 AND score_2 > 14)) AND 
                                    mapid = ?');
            $stmt->bind_param('sss', $tid, $tid, $getallmaps['mapid']);
            $stmt->execute();
            $results = $stmt->get_result();
            $mapswon = $results->fetch_assoc();

            // Amount of times map has been lost
            $stmt = $conn->prepare('SELECT COUNT(*) AS maploss FROM Playedmaps
                                    INNER JOIN
                                    (SELECT T.mid, tid_1, tid_2, pmapid FROM Matches
                                    INNER JOIN
                                    (SELECT * FROM Map_belongs_to) AS T
                                    ON Matches.mid = T.mid) AS TT
                                    ON TT.pmapid = Playedmaps.pmapid WHERE score_1 is not null AND score_2 is not null AND 
                                    ((tid_1 = ? AND score_1 < score_2 AND score_2 > 14) OR
                                    (tid_2 = ? AND score_1 > score_2 AND score_1 > 14)) AND 
                                    mapid = ?');
            $stmt->bind_param('sss', $tid, $tid, $getallmaps['mapid']);
            $stmt->execute();
            $results = $stmt->get_result();
            $mapslost = $results->fetch_assoc();

            $mapsplayed = $mapslost['maploss'] + $mapswon['mapwon'];
            echo "<tr>
            <td>". $getallmaps['name']. "</td>";
            echo "
            <td><font color='#2DBB00'>".$mapswon['mapwon']."</font>";
            echo " / ";
            echo "<font color='red'>".$mapslost['maploss']."</font>";
            if($mapsplayed == 0) {$divmap = 0;}
            else{$divmap = round($mapswon['mapwon']/$mapsplayed*$hund);}
            echo " (".$divmap."%)<br/></td>";

            }
            $conn->close();
            ?>
            </table>
            </div>
        </div>
    </div>

</div>
</div>
<?php include_once("footer.php") ?>
</body>

</html>