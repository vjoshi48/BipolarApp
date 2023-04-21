<?php

$dbServername = "localhost";
$dbUsername = "root";
$dbPassword = "varunj30";
$dbName = "bipolar";

$con = mysqli_connect($dbServername, $dbUsername, $dbPassword, $dbName, MYSQLI_ASYNC);

if (!$con) {
  die("Connection failed: " . mysqli_connect_error());
}

// // Define a custom error handler function
// function handle_sql_error($errno, $errstr, $errfile, $errline) {
//   // Redirect the user to an error page
//   header("Location: error.php");
//   exit;
// }

// Set the error handler function
// set_error_handler("handle_sql_error");

?>