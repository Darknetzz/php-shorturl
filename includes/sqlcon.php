<?php

$host = "127.0.0.1";
$user = "root";
$pass = "";
$db   = "short";

$sqlcon = new mysqli($host, $user, $pass, $db);

if ($sqlcon->connect_error) {
    die("Connection failed: " . $sqlcon->connect_error);
}

?>