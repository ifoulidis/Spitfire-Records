<?php

session_start();
if (!isset($_SESSION['admin_email'])) {

  echo "<script>window.open('log_in.php','_self')</script>";

} else {
  ?>

  <!DOCTYPE html>
  <html>

  <!-- add login functionality -->

  <head>
    <title>Dashboard</title>
    <link href="css/admin_style.css" rel="stylesheet">
    <style>
      body {
        background: url(../images/logo.png) no-repeat center center fixed;
        -webkit-background-size: cover;
        -moz-background-size: cover;
        -o-background-size: cover;
        background-size: cover;
      }
    </style>
  </head>

  <body>
    <div class="grid-container">
      <a href="./add_product_v2.php">
        <div class='card'>
          <h1>ADD PRODUCT</h1>
        </div>
      </a>
      <a href="./update_products.php">
        <div class='card'>
          <h1>UPDATE/DELETE PRODUCTS</h1>
        </div>
      </a>
      <a href="./orders.php">
        <div class='card'>
          <h1>Orders</h1>
        </div>
      </a>
  </body>

  </html>

<?php } ?>