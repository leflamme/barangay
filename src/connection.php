<?php 
// The host is the service name from docker-compose.yml
$host = 'db'; 

// These must match the 'environment' variables in docker-compose
$db_name = 'barangay'; 
$username = 'user';
$password = 'password';

// Create connection using MySQLi
$con = new mysqli($host, $username, $password, $db_name);

// Check connection
if ($con->connect_error) {
  die("Connection failed: " . $con->connect_error);
}
?>