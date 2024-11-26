<?php

// Set the default timezone
date_default_timezone_set('Pacific/Auckland');
$con = mysqli_connect("localhost", "root", "", "spitfire_records");

if ($con->connect_error) {
  echo "<script>console.log('Debug Objects: " . $con->connect_error . "' );</script>";
}

?>