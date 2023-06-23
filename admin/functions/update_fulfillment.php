<?php session_start();
if (!isset($_SESSION['admin_email'])) {
  echo "
<script>window.open('log_in.php', '_self')</script>";
} else {
  if (isset($_GET['id']) && isset($_GET['completion'])) {
    $con = mysqli_connect("localhost", "root", "", "spitfire records");

    $orderID = mysqli_real_escape_string($con, $_GET['id']);
    $completion = mysqli_real_escape_string($con, $_GET['completion']);
    $fulfillment_status = "complete";

    if ($completion == "complete") {
      $fulfillment_status = "pending";
    }

    // Prepare the SQL statement
    $updateQuery = "UPDATE orders SET fulfillment_status = ? WHERE id = ?";
    $statement = mysqli_prepare($con, $updateQuery);

    // Bind parameters
    mysqli_stmt_bind_param($statement, "si", $fulfillment_status, $orderID);

    // Execute the prepared statement
    $result = mysqli_stmt_execute($statement);

    if ($result) {
      // Update successful
      if ($fulfillment_status == "pending") {
        echo "Mark Complete";
      } else {
        echo "Undo";
      }
    } else {
      // Update failed
      echo "
<script>console.log('Error')</script>";
    }

    // Close the statement and connection
    mysqli_stmt_close($statement);
    mysqli_close($con);
  }
}
?>