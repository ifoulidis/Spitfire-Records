<?php
session_start();
include("includes/header.php");
include("includes/db.php");
include("send_email.php");

$customer_name = $_SESSION["customer_name"];

$paid = $_SESSION['price'];

$orders = "SELECT * FROM orders WHERE customer_name='$customer_name' and order_status='pending'";

$results = mysqli_query($db, $orders);

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
  $message_body .= "\nAddress: " . $row['street'] . ", " . $row['town'] . $row['zip'];
  $message_body .= "\nPhone: " . $row['phone'];
}

sendEmail($_SESSION['email'], $message_body);

echo "Paid: $" . $paid / 100;

$query = "update orders set order_status='complete' where customer_name='$customer_name' and order_status='pending'";

$update_orders = mysqli_query($db, $query);

?>

<main>
  <div class="home__container">
    <h1 class="purchase__heading">Thank you for your purchase,
      <?php echo $customer_name; ?>!
    </h1>
    <a href='http://localhost/SpitfireRecords/'>
      <h2>return to homepage</h2>
    </a>
  </div>
</main>
<?php
include("includes/footer.php");
?>
</body>



</html>