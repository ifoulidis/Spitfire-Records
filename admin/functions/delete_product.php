<?php session_start();
if (!isset($_SESSION['admin_email'])) {
  echo "
<script>window.open('log_in.php', '_self')</script>";
} else {
  if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $con = mysqli_connect("localhost", "spitfire_ezzierara", "mC75KFzdcAEEjmV*&", "spitfire_db_the_first");
    $deleteQuery = "DELETE FROM products where id = ?";
    $statement = mysqli_prepare($con, $deleteQuery);

    mysqli_stmt_bind_param($statement, 'i', $id);

    // Execute the prepared statement
    $result = mysqli_stmt_execute($statement);

    if ($result) {
      // Update successful
      echo "<script>console.log('Success')</script>";
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