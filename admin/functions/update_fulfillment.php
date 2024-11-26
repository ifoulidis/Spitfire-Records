<?php
session_start();
if (!isset($_SESSION['admin_email'])) {
  echo "<script>window.open('log_in.php', '_self')</script>";
} else {
  if (isset($_GET['id']) && isset($_GET['completion'])) {
    $con = mysqli_connect("localhost", "root", "", "spitfire_records");

    $orderID = mysqli_real_escape_string($con, $_GET['id']);
    $fulfillment_status = mysqli_real_escape_string($con, $_GET['completion']);

    // Prepare the SQL statement
    $updateQuery = "UPDATE orders SET fulfillment_status = ? WHERE id = ?";
    $statement = mysqli_prepare($con, $updateQuery);

    // Bind parameters
    mysqli_stmt_bind_param($statement, "si", $fulfillment_status, $orderID);

    // Execute the prepared statement
    $result = mysqli_stmt_execute($statement);

    if ($result) {
      $query = "SELECT * FROM orders WHERE id = ?";
      $stmt = mysqli_prepare($con, $query);

      mysqli_stmt_bind_param($stmt, "i", $orderID);
      mysqli_stmt_execute($stmt);

      // Declare variables for binding the result
      $id = $date = $product_id = $qty = $customer_name = $street = $town = $zip = $email = $phone = $invoice_no = $order_status = $fulfillment_status = "";

      // Bind result variables
      mysqli_stmt_bind_result($stmt, $id, $customer_name, $street, $town, $zip, $email, $phone, $invoice_no, $product_id, $qty, $order_status, $fulfillment_status, $date);

      // Fetch the result
      mysqli_stmt_fetch($stmt);

      // Update successful
      echo "<tr>";
      // Create the update link with the product ID as a query parameter
      echo "<td>" . $date . "</td>";
      echo "<td>" . $product_id . "</td>";
      echo "<td>" . $qty . "</td>";

      echo "<td>" . $customer_name . "</td>";
      echo "<td>" . $street . "</td>";
      echo "<td>" . $town . "</td>";
      echo "<td>" . $zip . "</td>";
      echo "<td>" . $email . "</td>";
      echo "<td>" . $phone . "</td>";
      echo "<td>" . $invoice_no . "</td>";
      echo "<td>" . $order_status . "</td>";
      echo "<td>" . $fulfillment_status . "</td>";
      // A button to change fulfillment_status to 'complete' for each row
      if ($fulfillment_status == "incomplete") {
        echo "<td><button class='markComplete' data-orderid='" . $id . "'>Mark Complete</button></td>";
      } else {
        echo "<td><button class='markComplete' data-orderid='" . $id . "'>Undo</button></td>";
      }
      echo "</tr>";



      // Close the statement and connection
      mysqli_stmt_close($statement);
      mysqli_stmt_close($stmt);
      mysqli_close($con);
    } else {
      // Update failed
      echo "<script>console.log('Error')</script>";

      // Close the connection
      mysqli_stmt_close($statement);
      mysqli_close($con);
    }
  }
}
?>