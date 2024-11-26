<?php
session_start();
date_default_timezone_set('Pacific/Auckland');
require_once 'includes/functions.php';
require_once 'vendor/autoload.php';
require_once 'secrets-v1.php';

$ip_add = getRealUserIp();
$shipping_rate = "";
// 30% discount on used CDs
// 30% discount on used CDs
$currentDateTime = new DateTime();
$startDateTime = new DateTime('2024-11-27 00:00:00'); // Wednesday, November 11, 2024, 12:00 AM
$endDateTime = new DateTime('2024-12-01 23:59:59');   // Sunday, December 1, 2024, 11:59 PM
$priceMultiplier = ($currentDateTime >= $startDateTime && $currentDateTime <= $endDateTime) ? 0.7 : 1;

if ($_SESSION['shipping_cost'] == 650) {
  // 
  $shipping_rate = "shr_1PbbT1LXNwr4Muvz5ops7wmk";
} else if ($_SESSION['shipping_cost'] == 1220) {
  //
  $shipping_rate = "shr_1PbbTiLXNwr4Muvza2Rl7JBt";
} else if ($_SESSION['shipping_cost'] == 1250) {
  //
  $shipping_rate = "shr_1PbbQnLXNwr4MuvzFdc7VrWJ";
} else if ($_SESSION['shipping_cost'] == 1820) {
  //
  $shipping_rate = "shr_1PbbRGLXNwr4MuvzP5mfKgHH";
} else if ($_SESSION['shipping_cost'] == 0) {
  $shipping_rate = "shr_1NaDPgLXNwr4MuvzMvQahylu";
}
$select_cart = "select * from cart where ip_add='$ip_add'";

$run_cart = mysqli_query($db, $select_cart);

$shipping = 0;
$is_vinyl = 0;
$price_before_shipping = 0;

while ($row_cart = mysqli_fetch_array($run_cart)) {


  $pro_id = $row_cart['p_id'];
  $pro_price = $row_cart['p_price'];
  $pro_qty = $row_cart['qty'];

  $select_product = "select * from products where id='$pro_id'";

  $run_product_find = mysqli_query($db, $select_product);

  while ($product_found = mysqli_fetch_array($run_product_find)) {
    $name_string = $product_found['album'] . ", " . $product_found['artist'];
    if ($product_found['new/used'] == 1 and $product_found['format'] === "CD") {
      $product_price = round($row_cart['p_price'] * $priceMultiplier, 2) * 100;
    }
    else{
      $product_price = $row_cart['p_price'] * 100;
    }
  }
  $dynamicProducts[] = ['name' => $name_string, 'price' => $product_price, 'quantity' => $pro_qty];

}


$lineItems = [];

// Create line items for each product
foreach ($dynamicProducts as $product) {
  $lineItems[] = [
    'price_data' => [
      'currency' => 'nzd',
      'unit_amount' => $product['price'],       // The price in cents
      'product_data' => [
        'name' => $product['name'],
      ],
    ],
    'quantity' => $product['quantity'],
  ];
}
\Stripe\Stripe::setApiKey($stripeSecretKey);

$session = \Stripe\Checkout\Session::create([
  'payment_method_types' => ['card'],
  "client_reference_id" => $_SESSION["invoice_number"],
  "customer_email" => $_SESSION['email'],
  'line_items' => $lineItems,
  'mode' => 'payment',
  'success_url' => 'https://spitfirerecords.co.nz/payment_complete.php',
  'cancel_url' => 'https://spitfirerecords.co.nz/cart.php',
  'shipping_options' => [
    [
      'shipping_rate' => $shipping_rate,
    ],
  ],
]);

header("Location: " . $session->url);