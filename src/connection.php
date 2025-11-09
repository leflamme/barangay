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
  // Log the error for you to see, but don't show it to the public
  error_log("Connection failed: " . $con->connect_error);
  // Show a generic message to the user
  die("There was a problem connecting to the service. Please try again later.");
}

/* ----- Local Docker SETUP -----
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