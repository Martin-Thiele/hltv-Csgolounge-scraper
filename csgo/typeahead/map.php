<?php
include_once("../connection.php");
    $key=$_GET['key'];
    $array = array();
    $query="select name from Maps where name LIKE '%{$key}%'";
    $result = $conn->query($query);
    while($row = $result->fetch_assoc())
    {
      $array[] = $row['name'];
    }
    echo json_encode($array);
?>
