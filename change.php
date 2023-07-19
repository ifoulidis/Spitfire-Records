<?php

session_start();

include("includes/db.php");

include("includes/functions.php");

?>

<?php


$ip_add = getRealUserIp();

$errors = array(); // Initialize an array to store error messages

// Check if the required POST parameters are set
if (isset($_POST['id']) && isset($_POST['quantity'])) {
  $id = mysqli_escape_string($db, $_POST['id']);
  $qty = mysqli_escape_string($db, $_POST['quantity']);

  // Validate input if needed
  // ...

  // Update quantity in the cart
  $change_qty = "UPDATE cart SET qty='$qty' WHERE p_id='$id' AND ip_add='$ip_add'";
  $run_qty = mysqli_query($db, $change_qty);

  if (!$run_qty) {
    // An error occurred while executing the query
    $errors[] = "Error updating quantity: " . mysqli_error($db);
  } else {
    echo "It worked";
  }
} else {
  // Required POST parameters are missing
  $errors[] = "Missing required parameters.";

}

if (!empty($errors)) {
  // Handle the errors (display, log, etc.)
  foreach ($errors as $error) {
    echo $error . "<br>";
  }
  // You can also redirect the user to an error page or perform other actions
  exit; // Stop further execution of the script
}



?>