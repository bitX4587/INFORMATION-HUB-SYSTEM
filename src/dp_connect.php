<?php
$servername = "localhost";
$username = "root";      // Change this if your database uses a different username
$password = "";          // Change this if your database uses a password
$dbname = "student";     // Name of your database

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>