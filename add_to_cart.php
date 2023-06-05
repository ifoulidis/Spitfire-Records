<?php

include("includes/functions.php");
include("includes/db.php");



if (isset($_POST['id'])) {

  $ip_add = getRealUserIp();


  $p_id = $_POST['id'];


  if (isset($_POST['qty'])) {
    $qty = $_POST['qty'];
  } else {
    $qty = 1;
  }



  $check_product = "select * from cart where ip_add='$ip_add' AND p_id='$p_id'";

  $run_check = mysqli_query($con, $check_product);

  if (mysqli_num_rows($run_check) > 0) {

    echo "<script>alert('This Product is already added in cart')</script>";

  } else {

    $get_price = "select * from products where id='$p_id'";

    $run_price = mysqli_query($con, $get_price);

    $row_price = mysqli_fetch_array($run_price);

    $pro_price = $row_price['regular_price'];

    $query = "insert into cart (p_id,ip_add,qty,p_price) values ('$p_id','$ip_add','$qty','$pro_price')";

    $run_query = mysqli_query($db, $query);

    $select_cart = "select * from cart where ip_add='$ip_add'";

    $run_cart = mysqli_query($con, $select_cart);

    echo mysqli_num_rows($run_cart);

  }

}

?>