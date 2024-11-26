<?php
session_start();
include("includes/header.php");
include("includes/db.php");
include("send_email.php");

if (!isset($_SESSION["customer_name"])) {
  echo "<div class='paymentComplete__container'>";
  echo "<h1 class='purchase__heading'>Your session has expired or something went wrong.</h1>";
  echo "<h1>Please contact us, as your payment may have gone through.</h1>";
  echo "<a href='mailto:spitfirerecordsnz@gmail.com'>spitfirerecordsnz@gmail.com</a>";
  echo "<a href='https://spitfirerecords.co.nz/contact_us.php'>Contact Us Page</a>";
  echo "</div>";
  exit;
}

$customer_name = $_SESSION["customer_name"];
$ip_address = getRealUserIp();

// Delete items from cart

$delete_cart = "delete from cart where ip_add='$ip_address'";

global $con;
$run_delete = mysqli_query($con, $delete_cart);

if (isset($_SESSION['price'])) {
  $paid = number_format(round($_SESSION['price'] / 100, 2), 2);
} else {
  echo "<h1>Amount missing. Please contact us. There may have been a problem.</h1>";
  echo "<a href='https://spitfirerecords.co.nz/contact_us.php'>Contact Us</a>";
}

$todayDate = date("Y-m-d");
if ($con->connect_error) {
    error_log("Connection failed: " . $con->connect_error);
}
$stmt = "SELECT * FROM orders WHERE customer_name='$customer_name' ORDER BY date DESC";
$results = mysqli_query($con, $stmt);


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
    $invoice_number = null;
    if ($results->num_rows === 0) {
        echo "<h1 class='purchase__heading'>Something went wrong when retrieving your order.</h1>";
        echo "<h1>Please contact us, as your payment may have gone through if you paid by Stripe.</h1>";
        echo "<a href='mailto:spitfirerecordsnz@gmail.com'>spitfirerecordsnz@gmail.com</a>";
        echo "<a href='https://spitfirerecords.co.nz/contact_us.php'>Contact Us</a>";
        echo "</div>";
    } else {
      // Find the latest record
      while ($row = mysqli_fetch_array($results)) {
        if ($invoice_number === null) {
            $invoice_number = $row['invoice_no'];
        }
      }
      // Reset the pointer back to the beginning of the result set
      mysqli_data_seek($results, 0);

      // Get info of each item just paid for or to pay for.
      while ($row = mysqli_fetch_array($results)) {
        if ($row['invoice_no'] !== $invoice_number) { 
            continue; // Skip processing and move to the next iteration
        }
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
          
          // 30% discount on used CDs
          $currentDateTime = new DateTime();
          $startDateTime = new DateTime('2024-11-27 00:00:00'); // Wednesday, November 11, 2024, 12:00 AM
          $endDateTime = new DateTime('2024-12-01 23:59:59');   // Sunday, December 1, 2024, 11:59 PM
          $priceMultiplier = ($currentDateTime >= $startDateTime && $currentDateTime <= $endDateTime) ? 0.7 : 1;
          if ($row_products['new/used'] == 1 and $row_products['format'] === "CD") {
            $pro_price = round($pro_price * $priceMultiplier, 2);
            $subtotal = number_format(round($pro_price * $qty, 2), 2);
          }
          else{
            $subtotal = number_format($pro_price * $qty, 2);
          }

          $customer_message_body .= $row_products['album'] . ", " . $row_products["artist"] . ", qty = " . $qty . " | $" . $subtotal . "\n";
          $owner_message_body .= $row_products['album'] . ", " . $row_products["artist"] . ", qty = " . $qty . " | $" . $subtotal . "\n";
          $line = "<h2 class='paymentComplete__text'>" . $row_products['album'] . ", " . $row_products["artist"] . ", qty = " . $qty . "| $" . $subtotal . "</h2>";

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
        $formattedShippingCost = number_format($_SESSION['shipping_cost'] / 100, 2);
        echo "Shipping cost: $" . $formattedShippingCost . " (" . $_SESSION['shipping_method'] . ")";
        $customer_message_body .= "\nShipping cost: $" . $_SESSION['shipping_cost'] / 100 . " (" . $_SESSION['shipping_method'] . ")";
        $customer_message_body .= "\nTotal cost: $" . $paid;
        $owner_message_body .= "Shipping cost: $" . $_SESSION['shipping_cost'] / 100;
        $owner_message_body .= "\nShipping type: " . $_SESSION['shipping_method'];
        $owner_message_body .= "\nTotal cost: $" . $paid;
      }

      if (isset($_SESSION['payment_type']) && $_SESSION['payment_type'] == "Bank") {
        $customer_message_body .= "\n\nPlease pay $" . $paid . " to 03-1369-0415710-04\n";
        $customer_message_body .= "Reference: $invoice_number\n\n";
      }
      if (isset($_SESSION['payment_type']) && $_SESSION['payment_type'] == "Stripe") {
        $customer_message_body .= "Invoice No: $invoice_number\n\n";
        $owner_message_body .= "\n\nInvoice No: $invoice_number\n";
      }

      $owner_message_body .= "\nPayment type: " . $type . "\nAddress: " . $address . " \n";
      $owner_message_body .= $phone;
      $customer_message_body .= "Please make sure your address is correct:\n" . $address;
      $customer_message_body .= "\n\nKind regards,\nSpitfire Records";
    }

    // Prevent the email contents from being changed if the page is reloaded.
    // Prevent the emails from sending every time the page reloads.
    if (!isset($_SESSION['emailfinished'])) {
      $_SESSION['owner_email_content'] = $owner_message_body;
      $_SESSION['customer_email_content'] = $customer_message_body;

      sendEmail($_SESSION['email'], $customer_message_body);
      sendEmail("spitfirerecordsnz@gmail.com", $owner_message_body);

      // This only needs to be done the first time the page loads also
      $update_orders = $con->prepare("UPDATE orders
      SET order_status = 'complete'
      WHERE customer_name = ? AND order_status = 'pending' AND invoice_no = ?
      ORDER BY date DESC;
      ");
      $update_orders->bind_param("ss", $customer_name, $invoice_number);
      $update_orders->execute();
      $update_orders->close();
    }
    $_SESSION['emailfinished'] = "yes";

    // Create the button and JQuery function to resend the email
    echo '<button id="resendEmailButton">Resend Email</button>
            <script>
              $(document).ready(function () {
                $("#resendEmailButton").mousedown(function () {
                  $.ajax({
                    url: "send_email.php",
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
    // Resend the email if the button above is clicked.
    if (isset($_POST['action']) and $_POST['action'] == 'resend') {
      sendEmail($_POST['email'], $_SESSION['customer_email_content']);
      sendEmail("spitfirerecordsnz@gmail.com", $_SESSION['owner_email_content']);
      $_POST['action'] = null;
    }

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