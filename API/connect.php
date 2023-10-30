<?php

$servername = "localhost";
$username = "root";
$password = "";
$database = "rapid_compiler_v2";

// Create a connection to the MySQL database
$conn = mysqli_connect($servername, $username, $password);

// Check if the connection was successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Create the database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $database";
if (mysqli_query($conn, $sql)) {
   // echo "Database created successfully.<br>";
} else {
 //   echo "Error creating database: " . mysqli_error($conn) . "<br>";
}

// Select the newly created or existing database
mysqli_select_db($conn, $database);



?>