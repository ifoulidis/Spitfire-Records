<?php

session_start();
include("includes/db.php");
include("includes/header.php");
include("send_email.php");

$ip_add = getRealUserIp();

$status = "pending";

// 50% discount on used compilation CDs
$currentDateTime = new DateTime();
$startDateTime = new DateTime('2025-01-15 00:00:00'); // Wednesday, January 15, 2025, 12:00 AM
$endDateTime = new DateTime('2025-01-31 23:59:59');   // Friday, January 31, 2025, 11:59 PM
$priceMultiplier = ($currentDateTime >= $startDateTime && $currentDateTime <= $endDateTime) ? 0.5 : 1;

if (isset($_GET['token'])) {
  echo '<script type="text/javascript">alert("Looks like something went wrong! Please try again.")</script>';
}

if (isset($_POST["email"])) {
  $_SESSION["current_order"] = [];
  $customer_name = $_POST["name"];
  $_SESSION["customer_name"] = $customer_name;
  $customer_email = $_POST["email"];
  $_SESSION['email'] = $customer_email;
  $customer_street = $_POST["street"];
  $customer_town = $_POST["town"];
  $customer_zip = $_POST["zip"];
  $customer_phone = $_POST["phone"];
  $shipping_type = $_POST["shipping_type"];
  $_SESSION['shipping_method'] = $shipping_type;

  $invoice_no = mt_rand(1, 100000);
  $_SESSION["invoice_number"] = $invoice_no;

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
    $_SESSION["current_order"][] = $pro_id;

    $run_product_find = mysqli_query($db, $select_product);

    while ($product_found = mysqli_fetch_array($run_product_find)) {
      if ($product_found['format'] === "Vinyl LP" || $product_found['format'] === '7" Vinyl') {
        $is_vinyl = 1;
      }
      // Discount
      if ($product_found['new/used'] == 1 and $product_found['format'] === "CD" and str_contains($product_found['artist'], 'Various')) {
        $pro_price = round(($row_cart['p_price'] * $pro_qty) * $priceMultiplier, 2);
      }
      echo "<script>console.log('$pro_price')</script>";
      $price_before_shipping += ($pro_price * $pro_qty) * 100;

    }

    // Add to orders table
    // The statement checks whether the same order already exists for this customer.
    // If it does, it is simply updated date-wise.
    // Otherwise, the order is added to the table.

    $insert_pending_order = "INSERT INTO orders (customer_name, street, town, zip, email, phone, invoice_no, product_id, qty, order_status, fulfillment_status, date)
      SELECT '$customer_name', '$customer_street', '$customer_town', '$customer_zip', '$customer_email', '$customer_phone', '$invoice_no', '$pro_id', '$pro_qty', '$status', 'incomplete', NOW()
      WHERE NOT EXISTS (
          SELECT 1 FROM orders WHERE customer_name = '$customer_name' AND product_id = $pro_id)
      ON DUPLICATE KEY UPDATE date = NOW()";

    $run_pending_order = mysqli_query($con, $insert_pending_order);
    if ($run_pending_order === false) {
      // An error occurred
      $error_message = mysqli_error($con);
      echo "<script>console.log('There was an error with your order. Please contact us!' );</script>";
    }

  }

  if ($shipping_type === "urban" and $is_vinyl === 0) {
    $shipping = 650;
  } else if ($shipping_type === "rural" and $is_vinyl === 0) {
    $shipping = 1220;
  } else if ($shipping_type === "urban" and $is_vinyl === 1) {
    $shipping = 1250;
  } else if ($shipping_type === "rural" and $is_vinyl === 1) {
    $shipping = 1820;
  } else if ($shipping_type === "pickup") {
    $shipping = 0;
  } else {
    $owner_message = "Shipping type was somehow not detected for " . $customer_name . ". Please contact via " . $customer_email . " if order was completed.";
    sendEmail("spitfirerecordsnz@gmail.com", $owner_message);
  }


  $_SESSION['shipping_cost'] = $shipping;
  $total_price = ($price_before_shipping + $shipping);
  $_SESSION['price'] = $total_price;


  if (isset($_POST['button'])) {
    if ($_POST['button'] == "PayStripe") {
      $_SESSION['payment_type'] = "Stripe";
    } elseif ($_POST['button'] == "Bank") {
      $_SESSION['payment_type'] = "Bank";
    }
  }
}

?>

<main>
  <style>
    body {
      font-family: Arial, sans-serif;
    }

    .orders__checkout-container {
      max-width: 600px;
      margin: 0 auto;
      padding: 20px;
      background-color: #f2f2f2;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .orders__column {
      display: flex;
      justify-content: center;
    }

    .orders__heading {
      text-align: center;
      font-size: 24px;
      margin-bottom: 20px;
    }

    .orders__label {
      display: block;
      margin-bottom: 8px;
      font-size: 14px;
      font-weight: bold;
    }

    .orders__input {
      width: 90%;
      padding: 10px;
      font-size: 14px;
      border: 1px solid #ccc;
      margin-bottom: 7px;
    }

    #fname,
    #email,
    #phone {
      border-radius: 4px;
    }

    #town {
      border-top-left-radius: 4px;
      border-bottom-left-radius: 4px;
    }

    #zip {
      border-top-right-radius: 4px;
      border-bottom-right-radius: 4px;
    }

    .orders__row {
      display: flex;
      width: 90%;
      justify-content: space-between;
    }

    .orders__btn {
      margin: 4px;
      display: inline-block;
      padding: 10px 20px;
      font-size: 16px;
      color: #fff;
      background-color: #ff0000;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .orders__btn:hover {
      background-color: #a00000;
    }

    .checkout__buttons {
      display: flex;
      justify-content: center;
      padding: 20px;
    }
  </style>
  <div class="orders__checkout-container">
    <form id="orderForm">
      <div class="orders__column">
        <div class="orders__col-50">
          <h3 class="orders__heading">Customer Information</h3>
          <label class="orders__label" for="fname">Full Name</label>
          <input type="text" id="fname" name="fullname" placeholder="John M. Doe" required class="orders__input">
          <label class="orders__label" for="email">Email</label>
          <input type="email" id="email" name="email" placeholder="john@example.com" required class="orders__input">
          <label class="orders__label" for="phone">Phone</label>
          <input type="phone" id="phone" placeholder="02100000000" name="phone" required class="orders__input">
          <label class="orders__label" for="street">Street</label>
          <input type="text" id="street" name="street" placeholder="2 Simple Street" required class="orders__input">

          <div class="orders__row">
            <div class="orders__col-50">
              <label class="orders__label" for="town">City or Town</label>
              <input type="text" id="town" name="town" placeholder="Auckland" required class="orders__input">
            </div>
            <div class="orders__col-50">
              <label class="orders__label" for="zip">Zip</label>
              <input type="number" id="zip" name="zip" placeholder="10001" required class="orders__input">
            </div>
          </div>
          <div class="orders__row">
            <div class="orders__location">
              <label>
                <input type="radio" name="location" value="urban" required> Urban
              </label>
            </div>
            <div class="orders__location-50">
              <label>
                <input type="radio" name="location" value="rural" required> Rural
              </label>
            </div>
            <div class="orders__location-50">
              <label>
                <input type="radio" name="location" value="pickup" required> Pick Up
              </label>
            </div>
          </div>
        </div>
      </div>
      <div class="checkout__buttons">
        <input type="submit" value="Debit/Credit" id="PayStripe" class="orders__btn">
        <input type="submit" value="Bank Transfer" id="Bank" class="orders__btn">
      </div>
    </form>
  </div>
</main>

</body>

<script>
  $(document).ready(function () {
    $('#PayStripe').click(function (event) {
      // Need to prevent default submission AND check validity:
      let isFormValid = $('#orderForm')[0].reportValidity(); // Use reportValidity() instead of checkValidity()
      if (!isFormValid) {
        event.preventDefault();
      } else {
        event.preventDefault(); // Prevent the default form submission

        var formData = {
          button: this.id,
          name: $("#fname").val(),
          email: $("#email").val(),
          phone: $("#phone").val(),
          street: $("#street").val(),
          town: $("#town").val(),
          zip: $("#zip").val(),
          shipping_type: $('input[name="location"]:checked').val()
        };

        $.ajax({
          url: window.location.href, // Send the request to the current page URL
          type: 'POST',
          data: formData,
          success: function (response) {
            if (response.includes('alert(')) {
              // Create a new div for the alert
              var $alert = $('<div class="alert">' + response + '</div>');

              // Append the alert to the body
              $('body').append($alert);

              // Add a click event to close the alert
              $alert.on('click', function () {
                $(this).remove();
              });
            }
            // Handle the response from PHP
            window.location.href = 'stripe_checkout.php'; // Navigate to stripe_checkout.php after successful response
          },
          error: function (xhr, status, error) {
            // Handle errors
            console.log(error);
          }
        });
      }
    });

    $('#Bank').click(function () {
      // Need to prevent default submission AND check validity:
      let isFormValid = $('#orderForm')[0].reportValidity(); // Use reportValidity() instead of checkValidity()
      if (!isFormValid) {
        event.preventDefault();
      } else {
        event.preventDefault(); // Prevent the default form submission

        var formData = {
          button: this.id,
          name: $("#fname").val(),
          email: $("#email").val(),
          phone: $("#phone").val(),
          street: $("#street").val(),
          town: $("#town").val(),
          zip: $("#zip").val(),
          shipping_type: $('input[name="location"]:checked').val()
        };

        $.ajax({
          url: 'order.php',
          type: 'POST',
          data: formData,
          success: function () {
            // Handle the response from PHP
            window.location.href = 'payment_complete.php'; // Navigate to stripe_checkout.php after successful response
          },
          error: function (xhr, status, error) {
            // Handle errors
            console.log(error);
          }
        });
      }
    });
  });
</script>

</html>