<?php
// Create connection to database
$servername = "localhost";
$username = "127.0.0.1";
$password = "127.0.0.1";
$dbname = "db";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$url = "//localhost/csgo";
$navurl = "/csgo/";
?>