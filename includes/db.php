<?php

$con = mysqli_connect("localhost", "root", "", "spitfire records");

if ($con->connect_error) {
  echo "<script>console.log('Debug Objects: " . $con->connect_error . "' );</script>";
}

?>