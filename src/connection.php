<?php 
$host = getenv('MYSQLHOST'); 
$db_name = getenv('MYSQLDATABASE'); 
$username = getenv('MYSQLUSER');
$password = getenv('MYSQLPASSWORD');
$port = getenv('MYSQLPORT');

echo "Host: '" . $host . "'<br>";
echo "DB Name: '" . $db_name . "'<br>";
echo "User: '" . $username . "'<br>";
echo "Password is set: " . (empty($password) ? "NO" : "YES") . "<br>";
echo "Port: '" . $port . "'<br>";

echo "--- ENDING DEBUG ---<br><br>";

// Create connection using MySQLi
$con = new mysqli($host, $username, $password, $db_name, $port);

// Check connection
if ($con->connect_error) {
  die("Connection failed: " . $con->connect_error);
} else {
  echo "SUCCESS! Connected to the database.";
}


/* ----- 1st Railway SETUP -----
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
  die("Connection failed: " . $con->connect_error);
}
*/

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