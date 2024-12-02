<?php
$host = '153.92.15.25';
$username = 'u843230181_SclcApp';
$password = 'Sclc1111';
$database = 'u843230181_SCLC';

// Create a connection
$connection = mysqli_connect($host, $username, $password, $database);

// Check the connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}
?>
