<?php
session_start();

require_once('vendor/autoload.php');
require_once('secrets.php');

\Stripe\Stripe::setApiKey($stripeSecretKey);


$price = $_SESSION['price'];
$customer_email = $_SESSION['email'];

header('Content-Type: application/json');

try {

  // Create a PaymentIntent with amount and currency
  $paymentIntent = \Stripe\PaymentIntent::create([
    'amount' => $price,
    'currency' => 'nzd',
    'receipt_email' => $customer_email,
    'automatic_payment_methods' => [
      'enabled' => true,
    ],
  ]);

  $output = [
    'clientSecret' => $paymentIntent->client_secret,
  ];

  echo json_encode($output);
} catch (Error $e) {
  http_response_code(500);
  echo json_encode(['error' => $e->getMessage()]);
}

?>