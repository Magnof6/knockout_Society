<?php
$servername = "serverkn.ddns.net";
$username = "root";
$password = "1234";
$dbname = "knockout";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>