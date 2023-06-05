<?php
session_start();
include("includes/header.php");
include("includes/db.php");

$customer_name = $_SESSION["customer_name"];

$paid = $_SESSION['price'];
// $products = $_SESSION['products'];
// echo var_dump($_SESSION['products']);

// foreach ($products as $key => $product) {
//   $title = $product['title'];
//   $quantity = $product['quantity'];
//   // Perform actions using $title and $quantity
//   // ...
//   echo "Title = $title<br>";
//   echo "Quantity = $quantity<br>";
// }
echo "Paid: $" . $paid / 100;

$query = "update orders set order_status='complete' where customer_name='$customer_name' and order_status='pending'";

$update_orders = mysqli_query($con, $query);

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