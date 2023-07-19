<?php

$con = mysqli_connect("localhost", "spitfire_ezzierara", "mC75KFzdcAEEjmV*&", "spitfire_db_the_first");

if ($con->connect_error) {
  echo "<script>console.log('Debug Objects: " . $con->connect_error . "' );</script>";
}

?>