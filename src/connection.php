<?php 
// Read credentials from Environment Variables provided by Railway
$host = getenv('MYSQL_HOST'); 
$db_name = getenv('MYSQL_DATABASE'); 
$username = getenv('MYSQL_USER');
$password = getenv('MYSQL_PASSWORD');
$port = getenv('MYSQL_PORT');

// Create connection using MySQLi
$con = new mysqli($host, $username, $password, $db_name, $port);

// Check connection
if ($con->connect_error) {
  die("Connection failed: " . $con->connect_error);
}

/*
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
*/
?>