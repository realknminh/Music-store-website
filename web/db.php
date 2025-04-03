<?php

$db_server = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "music_store";
$db_port = 3307;

$conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name, $db_port);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
