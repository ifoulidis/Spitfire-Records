<?php
session_start();
include("includes/header.php");
include("includes/db.php");
include("send_email.php");

$customer_name = $_SESSION["customer_name"];
$ip_address = getRealUserIp();

// Delete items from cart

$delete_cart = "delete from cart where ip_add='$ip_address'";

$run_delete = mysqli_query($con, $delete_cart);

if (isset($_SESSION['price'])) {
  $paid = $_SESSION['price'] / 100;
} else {
  $paid = "Amount missing. Please contact us. There may have been a problem.";
}

$orders = "SELECT * FROM orders WHERE customer_name='$customer_name' and order_status='pending'";

$results = mysqli_query($con, $orders);

$owner_message_body = $customer_name . " purchased:\n";
$customer_message_body = "Hi " . $customer_name . ",\n\nYou purchased:\n";
$type = $_SESSION['payment_type'];
?>

<main>
  <div class="paymentComplete__container">
    <?php
    if (isset($_SESSION['payment_type'])) {
      if ($_SESSION['payment_type'] == "Bank") {
        echo "<h1 class='purchase__heading'>Thank you for your purchase,
      $customer_name
    </h1><br>
    <h2 class='paymentComplete__text'>
      Please pay $$paid to the account number sent to your email address.\n Be Sure to check your spam folder if it does not appear promptly.
    </h2>";
      } else if ($_SESSION['payment_type'] == "Stripe") {
        echo "<h1 class='purchase__heading'>Thank you for your purchase, $customer_name </h1><br>";
      }
    } else {
      echo "<h1 class='purchase__heading'>Warning! There might have been a problem with your purchase,
      $customer_name!
    </h1><br>
    <h2 class='paymentComplete__text'>
      Please check your email and <a href='https://spitfirerecords.co.nz/contact_us.php'>Contact Us</a> if necessary.
    </h2>";
    }
    $invoice_number = 0;
    $_SESSION['product_lines'] = '<br>';

    // Get info of each item just paid for or to pay for.
    while ($row = mysqli_fetch_array($results)) {
      $product_id = $row['product_id'];
      $invoice_number = $row['invoice_no'];
      $address = $row['street'] . ", " . $row['town'] . " " . $row['zip'];
      $phone = "\nPhone: " . $row['phone'] . "\n";
      $get_products = "SELECT * FROM products WHERE id=$product_id";
      $run_products = mysqli_query($con, $get_products);
      $qty = $row['qty'];

      // Pull info of product for email and this page.
      while ($row_products = mysqli_fetch_array($run_products)) {
        $pro_price = $row_products['regular_price'];
        $price_for_product = $pro_price * $qty;
        $customer_message_body .= $row_products['album'] . ", " . $row_products["artist"] . ", qty = " . $qty . " | $" . $price_for_product . "\n";
        $owner_message_body .= $row_products['album'] . ", " . $row_products["artist"] . ", qty = " . $qty . " | $" . $price_for_product . "\n";
        $line = "<h2 class='paymentComplete__text'>" . $row_products['album'] . ", " . $row_products["artist"] . ", qty = " . $qty . "| $" . $price_for_product . "</h2>";

        echo $line;
        echo "<br>";
      }

      // Remove stock.
      $update_products = "UPDATE products
      SET stock = stock - 1
      WHERE id=$product_id";
      $update_products_execute = mysqli_query($con, $update_products);
    }

    if (isset($_SESSION['shipping_cost']) and isset($_SESSION['shipping_method'])) {
      $formattedShippingCost = number_format(($_SESSION['shipping_cost'] / 100), 2);
      echo "Shipping cost: $" . $formattedShippingCost . " (" . $_SESSION['shipping_method'] . ")";
      $customer_message_body .= "\nShipping cost: $" . $_SESSION['shipping_cost'] / 100 . " (" . $_SESSION['shipping_method'] . ")";
      $customer_message_body .= "\nTotal cost: " . $paid;
      $owner_message_body .= "Shipping cost: $" . $_SESSION['shipping_cost'] / 100;
      $owner_message_body .= "\nShipping type: " . $_SESSION['shipping_method'];
      $owner_message_body .= "\nTotal cost: " . $paid;
    }

    if (isset($_SESSION['payment_type']) && $_SESSION['payment_type'] == "Bank") {
      $customer_message_body .= "\n\nPlease pay $" . $paid . " to 03-1369-0415710-04\n";
      $customer_message_body .= "Reference: $invoice_number\n\n";
    }
    if (isset($_SESSION['payment_type']) && $_SESSION['payment_type'] == "Stripe") {
      $customer_message_body .= "Invoice No: $invoice_number\n\n";
    }

    $owner_message_body .= "\nPayment type: " . $type . "\nAddress: " . $address . " \n";
    $owner_message_body .= $phone;
    $customer_message_body .= "Please make sure you address is correct:\n" . $address;
    $customer_message_body .= "\n\nKind regards,\nSpitfire Records";

    $_SESSION['owner_email_content'] = $owner_message_body;
    $_SESSION['customer_email_content'] = $customer_message_body;
    sendEmail($_SESSION['email'], $customer_message_body);
    sendEmail("spitfirerecordsnz@gmail.com", $owner_message_body);
    echo '<button id="resendEmailButton">Resend Email</button>
            <script>
              $(document).ready(function () {
                $("#resendEmailButton").mousedown(function () {
                  $.ajax({
                    url: "send_email.php", // Replace with the actual PHP file that handles the email sending
                    type: "POST",
                    data: { action: "resend"},
                    success: function () {
                      alert("Email resent!");
                    },
                    error: function () {
                      alert("Error occurred while resending email.");
                    }
                  });
                });
              });
            </script>';
    if (isset($_POST['action']) and $_POST['action'] == 'resend') {
      sendEmail($_POST['email'], $_SESSION['customer_email_content']);
      $_POST['action'] = null;
    }


    $query = "update orders set order_status='complete' where customer_name='$customer_name' and order_status='pending'";

    $update_orders = mysqli_query($con, $query);

    ?>


    <br>
    <a href='https://spitfirerecords.co.nz/' style="color:red;">
      <h1>Return to Homepage</h1>
    </a>
  </div>

</main>
<?php
include("includes/footer.php");
?>
</body>
<script>
  $(document).ready(function () {
    $("#cartCount").html(0);
  })
</script>


</html>