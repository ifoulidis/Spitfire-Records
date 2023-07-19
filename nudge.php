<?php
require_once realpath(__DIR__ . "/vendor/autoload.php");
include("send_email.php");
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$POLiSecretKey = $_ENV['POLiSecretKey'];

if (isset($_POST['Token'])) {
  $token = $_POST["Token"];
  if (is_null($token)) {
    $token = $_GET["token"];
  }

  $auth = base64_encode($POLiSecretKey);
  $header = array();
  $header[] = 'Authorization: Basic ' . $auth;

  $ch = curl_init("https://poliapi.apac.paywithpoli.com/api/v2/Transaction/GetTransaction?token=" . urlencode($token));
  //See the cURL documentation for more information: http://curl.haxx.se/docs/sslcerts.html
  //We recommend using this bundle: https://raw.githubusercontent.com/bagder/ca-bundle/master/ca-bundle.crt
  curl_setopt($ch, CURLOPT_CAINFO, "ca-bundle.crt");
  curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_POST, 0);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  $response = curl_exec($ch);
  curl_close($ch);

  $json = json_decode($response, true);

  $paid = $json['Amount'];
  $invoice_number = $json['MerchantReference'];

  $orders = "SELECT * FROM orders WHERE invoice_no='$invoice_number' and order_status='pending'";

  $results = mysqli_query($db, $orders);
  $email_address = "";
  while ($row = mysqli_fetch_array($results)) {
    $email_address = $row['email'];
  }

  switch ($json['TransactionStatusCode']) {
    case "Completed":

      $message_body = $customer_name . " has purchased:\n";

      while ($row = mysqli_fetch_array($results)) {
        $product_id = $row['product_id'];
        $get_products = "SELECT * FROM products WHERE id='$product_id'";
        $run_products = mysqli_query($db, $get_products);

        while ($row_products = mysqli_fetch_array($run_products)) {
          $message_body .= $row_products['album'] . ", " . $row_products["artist"] . ", qty = " . $row['qty'] . "\n";
          echo $row_products['album'] . ", " . $row_products["artist"] . ", qty = " . $row['qty'];
          echo "<br>";
        }
        $update_products = "UPDATE products
          SET stock -= 1
          WHERE id='$product_id'";
        $update_products_execute = mysqli_query($db, $update_products);

        $message_body .= "\nAddress: " . $row['street'] . ", " . $row['town'] . $row['zip'];
        $message_body .= "\nPhone: " . $row['phone'];
      }

      sendEmail($email_address, $message_body);

      $query = "update orders set order_status='complete' where customer_name='$invoice_number' and order_status='pending'";

      $update_orders = mysqli_query($db, $query);

    case 'Cancelled':
      sendEmail($email_address, "Did you mean to cancel your payment?\nPlease contact us at spitfirerecordsnz@gmail.com if something went wrong.");
    case 'Failed':
      sendEmail($email_address, "Something may have gone wrong on the POLi end. Please contact us through the contact page, and we will get in touch.");
    case 'ReceiptUnverified':
      sendEmail($email_address, "Something may have gone wrong on the POLi end. Please contact us through the contact page, and we will get in touch.");
    case 'TimedOut':
      sendEmail($email_address, 'Your payment timed out.\nPlease try again or contact us at spitfirerecordsnz@gmail.com if something went wrong.\n');
  }
}