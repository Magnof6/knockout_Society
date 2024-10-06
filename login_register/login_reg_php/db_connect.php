<?php
$servername = "serverkn.ddns.net";
$username = "root";
$password = "1234";
$dbname = "knockout";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>