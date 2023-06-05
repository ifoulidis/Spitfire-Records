<?php

session_start();
include("includes/db.php");
include("includes/header.php");

require_once realpath(__DIR__ . "/vendor/autoload.php");
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$POLiSecretKey = $_ENV['POLiSecretKey'];

$ip_add = getRealUserIp();

$status = "pending";

if (isset($_POST["email"])) {
  $customer_name = $_POST["name"];
  $_SESSION["customer_name"] = $customer_name;
  $customer_email = $_POST["email"];
  $customer_street = $_POST["street"];
  $customer_town = $_POST["town"];
  $customer_zip = $_POST["zip"];
  $customer_phone = $_POST["phone"];

  $invoice_no = mt_rand();

  $select_cart = "select * from cart where ip_add='$ip_add'";

  $run_cart = mysqli_query($con, $select_cart);


  while ($row_cart = mysqli_fetch_array($run_cart)) {

    $pro_id = $row_cart['p_id'];

    $pro_qty = $row_cart['qty'];

    $sub_total = $row_cart['p_price'] * $pro_qty;

    $insert_pending_order = "insert into orders (customer_name,street,town,zip,email,phone,invoice_no,product_id,qty,order_status,fulfillment_status,date) values ('$customer_name','$customer_street','$customer_town','$customer_zip','$customer_email','$customer_phone','$invoice_no','$pro_id','$pro_qty','$status','incomplete',NOW())";

    $run_pending_order = mysqli_query($con, $insert_pending_order);

    $delete_cart = "delete from cart where ip_add='$ip_add'";

    $run_delete = mysqli_query($con, $delete_cart);
  }

}

if (isset($_POST['button'])) {
  if ($_POST['button'] == "PayStipe") {
    //
  } elseif ($_POST['button'] == "PayPOLi") {
    $json_builder = '{
    "Amount":' . $_SESSION["price"] / 100 . ',
    "CurrencyCode":"NZD",
    "MerchantReference":' . $invoice_no . ',
    "MerchantHomepageURL":"http://localhost/SpitfireRecords/",
    "SuccessURL":"http://localhost/SpitfireRecords/payment_complete",
    "FailureURL":"http://localhost/SpitfireRecords/order.php",
    "CancellationURL":"http://localhost/SpitfireRecords/cancel_payment.php",
    "NotificationURL":"http://localhost/SpitfireRecords/order.php" 
}';
    // Make secret before production!
    $auth = base64_encode($POLiSecretKey);
    $header = array();
    $header[] = 'Content-Type: application/json';
    $header[] = 'Authorization: Basic ' . $auth;

    $ch = curl_init("https://poliapi.apac.paywithpoli.com/api/v2/Transaction/Initiate");
    //See the cURL documentation for more information: http://curl.haxx.se/docs/sslcerts.html
//We recommend using this bundle: https://raw.githubusercontent.com/bagder/ca-bundle/master/ca-bundle.crt
    curl_setopt($ch, CURLOPT_CAINFO, "ca-bundle.crt");
    curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_builder);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);

    $json = json_decode($response, true);

    header('Location: ' . $json["NavigateURL"]);
  }
}

?>

<div style="display: flex; justify-content: center;">
  <div class="col-75" style="max-width: 600px;">
    <div class="container">
      <form action="/action_page.php" style="text-align: left;">

        <div class="row">
          <div class="col-50">
            <h3 style="text-align: center;">Customer Information</h3>
            <label for="fname"><i class="fa fa-user"></i> Full Name</label>
            <input type="text" id="fname" name="fullname" placeholder="John M. Doe" required>
            <label for="email"><i class="fa fa-envelope"></i> Email</label>
            <input type="email" id="email" name="email" placeholder="john@example.com" required>
            <label for="street"><i class="fa fa-address-card-o"></i> Street</label>
            <input type="text" id="street" name="street" placeholder="2 Simple Street" required>
            <label for="phone"><i class="fa fa-address-card-o"></i> Phone</label>
            <input type="phone" id="phone" name="phone" required>

            <div class="row">
              <div class="col-50">
                <label for="town">City or Town</label>
                <input type="text" id="town" name="town" placeholder="Auckland" required>
              </div>
              <div class="col-50">
                <label for="zip">Zip</label>
                <input type="number" id="zip" name="zip" placeholder="10001" required>
              </div>
            </div>
          </div>
        </div>
        <div style="text-align: center; margin-top: 20px;">
          <input type="submit" value="Pay With Stripe" id="PayStripe" class="btn">
          <input type="submit" value="Pay With POLi" id="PayPOLi" class="btn">
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  $(document).ready(function () {
    $('#PayStripe').click(function (event) {
      event.preventDefault(); // Prevent the default form submission

      var formData = {
        button: this.id,
        name: $("#fname").val(),
        email: $("#email").val(),
        phone: $("#phone").val(),
        street: $("#street").val(),
        town: $("#town").val(),
        zip: $("#zip").val()
      };

      $.ajax({
        url: window.location.href, // Send the request to the current page URL
        type: 'POST',
        data: formData,
        success: function (response) {
          // Handle the response from PHP
          console.log(response);
          window.location.href = 'Stripe_checkout.php'; // Navigate to Stripe_checkout.php after successful response
        },
        error: function (xhr, status, error) {
          // Handle errors
          console.log(error);
        }
      });
    });

    $('#PayPOLi').click(function () {
      $.ajax({
        url: 'order.php',
        type: 'POST',
        data: formData,
        success: function (response) {
          // Handle the response from PHP
          console.log(response);
        },
        error: function (xhr, status, error) {
          // Handle errors
          console.log(error);
        }
      });
    });
  });
</script>