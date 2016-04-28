<?php
include_once("../connection.php");
    $key=$_GET['key'];
    $array = array();
    $query="SELECT name, Subcomp.subcid FROM Subcomp
    LEFT OUTER JOIN Comp_belongs_to
    ON Subcomp.subcid = Comp_belongs_to.subcid
    WHERE Comp_belongs_to.cid IS null AND name LIKE '%{$key}%'";
    $result = $conn->query($query);
    while($row = $result->fetch_assoc())
    {
      $array[] = $row['name'];
    }
    echo json_encode($array);
?>
