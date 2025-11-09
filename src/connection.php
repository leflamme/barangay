<?php 
// Read credentials from Environment Variables provided by Railway
$host = getenv('MYSQLHOST'); 
$db_name = getenv('MYSQLDATABASE'); 
$username = getenv('MYSQLUSER');
$password = getenv('MYSQLPASSWORD');
$port = getenv('MYSQLPORT');

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