<?php

session_start();

include("includes/db.php");

include("includes/functions.php");

?>

<?php


$ip_add = getRealUserIp();

if (isset($_POST['id']) and isset($_POST['quantity'])) {

  $id = $_POST['id'];

  $qty = $_POST['quantity'];

  $change_qty = "update cart set qty='$qty' where p_id='$id' AND ip_add='$ip_add'";

  $run_qty = mysqli_query($con, $change_qty);


}





?>