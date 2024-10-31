<?php

// Database host
$host = "127.0.0.1";

// Database username
$user = "root";

// Database password
$pass = "";

// Database name
$db   = "short";

// Create a new MySQLi connection
$sqlcon = new mysqli($host, $user, $pass, $db);

// Check the connection
if ($sqlcon->connect_error) {
    die("Connection failed: " . $sqlcon->connect_error);
}

?>