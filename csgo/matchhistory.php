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






        <!-- Recent matches pane //-->
<?php
$tid = ($_GET['tid']);
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
ORDER BY match_date desc, match_time');
        $stmt->bind_param('ss', $tid, $tid);
        $stmt->execute();
        $results = $stmt->get_result();
echo "<div class='wrapright' style='text-align: center; width: 360px; margin-right: 12px;'>";
echo "<h2>Recent matches</h2>";
echo "<div class='table table-hover' style='width: 360px; overflow:hidden; margin: 0 auto'>";
echo "<table class='table table-hover'>";
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
echo "</table></div></div>";


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