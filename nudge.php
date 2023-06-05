<?php

if (isset($_POST['Token'])) {
  $token = $_POST["Token"];
  if (is_null($token)) {
    $token = $_GET["token"];
  }

  $auth = base64_encode('T64010188:Ug5!EYrxw8Nv');
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

  function phpAlert($msg)
  {
    echo '<script type="text/javascript">alert("' . $msg . '")</script>';
  }

  switch ($json['TransactionStatusCode']) {
    case "Completed":
      header('Location: http://localhost/SpitfireRecords/payment_complete.php');
    case 'Cancelled':
      header('Location: http://localhost/SpitfireRecords/cancel_payment.php');
    case 'Failed':
      header('Location: http://localhost/SpitfireRecords/cancel_payment.php');
    case 'ReceiptUnverified':
      phpAlert("Something may have gone wrong on the POLi end. Please contact us through the contact page, and we will get in touch.");
    case 'TimedOut':
      header('Location: http://localhost/SpitfireRecords/cancel_payment.php');
  }
}