<html>
<head>
<?php
error_reporting(0);
include_once "navbar.php";
include_once "connection.php";
?>
<title>Dominations</title>
<link rel="stylesheet" type="text/css" href="css/style.css">
<link href="bootstrap/css/bootstrap.css" rel="stylesheet">
</head>
<body>
<div class="wrap">
    <div>
<h1>Dominations</h1>
<div class="table table-hover" >
<?php



$sql = "SELECT Maps.name as mapname, mid, name1, logo1, name2, logo2, Maps.mapid, score_1, score_2, match_date, match_time, T6.name, csgl, csglodds1, csglodds2, hltv FROM Maps
INNER JOIN

(SELECT mid, name1, logo1, name2, logo2, mapid, score_1, score_2, match_date, match_time, name, csgl, csglodds1, csglodds2, hltv FROM Subcomp
INNER JOIN
(SELECT mid, name1, logo1, name as name2, tcountry as logo2, mapid, score_1, score_2, match_date, match_time, subcid, csgl, csglodds1, csglodds2, hltv FROM Teams
INNER JOIN
(SELECT mid, name as name1, tcountry as logo1, tid_2, mapid, score_1, score_2, match_date, match_time, subcid, csgl, csglodds1, csglodds2, hltv FROM Teams
INNER JOIN
(SELECT TT.mid, tid_1, tid_2, mapid, score_1, score_2, match_date, match_time, subcid, csgl, csglodds1, csglodds2, hltv FROM Matches 
INNER JOIN 
(SELECT Map_belongs_to.pmapid as pmapid, Map_belongs_to.mid as mid, score_1, score_2, mapid FROM Map_belongs_to 
INNER JOIN 
(SELECT * FROM Playedmaps WHERE (score_1 = 16 AND score_2 = 0) OR (score_2 = 16 AND score_1 = 0)) AS T 
ON Map_belongs_to.pmapid = T.pmapid) AS TT 
ON TT.mid = Matches.mid) AS TTT
ON TTT.tid_1 = Teams.tid) AS T4
ON T4.tid_2 = Teams.tid) AS T5
ON T5.subcid = Subcomp.subcid) AS T6
ON T6.mapid = Maps.mapid
ORDER BY Match_date desc, match_time desc, mid asc
";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table class='table table-hover' ><thead><tr><th><b>Date</b></th>
                    <th><b>Time</b></th>
                    <th><b>BO</b></th>
                    <th class='text-right'><b>Team 1</b></th>
                    <th class='text-center'>%</th>
                    <th class='text-center'>#</th>
                    <th class='text-center'><b>-</b></th>
                    <th class='text-center'>#</th>
                    <th class='text-center'>%</th>
                    <th class='text-left'><b>Team 2</b></th>
                    <th><b>Matchpage</b></th>
                    </tr></thead>";
    while($row = $result->fetch_assoc()) {
    

        // hltv & csgl formatting
        $hltv = "";
        if($row['hltv']){$hltv = "<a href='".$row['hltv']."' title='Hltv'><img src='//brintos.dk/csgo/imgs/hltv.png'></a>";}
        $csgl = "";
        if($row['csgl']){$csgl = "<a href='".$row['csgl']."' title='Csgolounge'><img src='//brintos.dk/csgo/imgs/csgl.png'></a>";}


        // flag and date
        $teamflag  = str_replace(' ', '', (strtolower($row["logo1"])));
        $teamflag2 = str_replace(' ', '', (strtolower($row["logo2"])));
        $newDate   = date("d.m.Y", strtotime($row['match_date']));

       // name and comp formatting
        $name1 = $row['team_1'];
        $name2 = $row['team_2'];
       if(strlen($name1) > 15){$name1 = substr($name1, 0, 12) . ".." ;}
       if(strlen($name2) > 15){$name2 = substr($name2, 0, 12) . ".." ;}
       $comp = $row['name'];
       if(strlen($comp) > 13){$comp = substr($comp, 0, 9) . ".." ;}


       // Odds handling
       if($row['csglodds1'] == 0){$row['csglodds1'] = "";} else {$row['csglodds1'] = $row['csglodds1']."%";}
       if($row['csglodds2'] == 0){$row['csglodds2'] = "";} else {$row['csglodds2'] = $row['csglodds2']."%";}

       // Display handling for BO1
        $bomap   = $row['mapname'];
				$t1win   = $row['score_1'];
				$t2win   = $row['score_2'];


    

       // CSS formatting
       if($t1win > $t2win){$winloss = "win"; $winloss2 = "loss";}
       else if($t1win < $t2win){$winloss = "loss"; $winloss2 = "win";}
       else if($t1win != 0 && $t2win != 0){ $winloss ="draw"; $winloss2 = "draw";}
       else{$t1win = "&nbsp"; $t2win = "&nbsp"; $winloss ="draw"; $winloss2 = "draw";}
        echo "<tr><td>".$newDate."</td>
                  <td>".$row["match_time"]."</td>
                  <td>".$bomap."</td>
                  <td class='text-right'><a Title = \"". $row['name1'] . "\" href='teamid.php?tid=".$row["tid_1"]."'>".$row['name1']."</a> <img src='flags/".$teamflag.".png' alt='".ucfirst($row["logo1"])."' title='".ucfirst($row["logo1"])."'></td>
                  <td class='text-center'>".$row['csglodds1']."</td>
                  <td class='".$winloss."'>".$t1win."</td>
                  <td class='text-center'>-</td>
                  <td class='".$winloss2."'>".$t2win."</td>
                  <td class='text-center'>".$row['csglodds2']."</td>
                  <td class='text-left'><img src='flags/".$teamflag2.".png' alt='".ucfirst($row["logo2"])."' title='".ucfirst($row["logo2"])."'> <a Title = \"". $row['name2'] . "\" href='teamid.php?tid=".$row["tid_2"]."'>".$row['name2']."</a></td>
                  <td>".$hltv. " ". $csgl ." <a href='matchid.php?mid=".$row["mid"]."' Title = \"". $row["name"] . "\" >".$comp."</a></td></tr>";
  
      }
    echo "</table>";
} else {
    echo "No matches found";
}

$conn->close();
?>
</div>
</div>
</div>
<?php include_once("footer.php") ?>
</body>
</html>