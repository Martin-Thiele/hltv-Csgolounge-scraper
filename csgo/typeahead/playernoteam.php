<?php
    include_once("../connection.php");
    $key=$_GET['key'];
    $array = array();
    $query="SELECT ign FROM Players where ign LIKE '%{$key}%'";
    $result = $conn->query($query);
    while($row = $result->fetch_assoc())
    {
      $array[] = $row['ign'];
    }
    echo json_encode($array);
?>
