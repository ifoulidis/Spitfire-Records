<?php

session_start();
if (!isset($_SESSION['admin_email'])) {

  echo "<script>window.open('log_in.php','_self')</script>";

} else {
  ?>

  <!DOCTYPE html>
  <html>

  <!-- add login functionality -->

  <head>
    <title>Update Products</title>
    <link href="css/admin_style.css" rel="stylesheet">
  </head>

  <body>

    <h2>Update Products</h2>
    <h3>Leave fields blank if they need to be empty</h3>
    <table>
      <tr>
        <th>Update</th>
        <th>Product ID</th>
        <th>Album</th>
        <th>Artist</th>
        <th>Price</th>
        <th>Media Condition</th>
        <th>Sleeve/Insert Condition</th>
        <th>Format</th>
        <th>Pressing Year</th>
        <th>Stock</th>
        <th>Remove</th>
      </tr>

      <?php
      $con = mysqli_connect("localhost", "root", "", "spitfire records");

      // Retrieve the first 50 products
      $query = "SELECT * FROM products LIMIT 50";
      $result = mysqli_query($con, $query);

      // Display the products in a table
      while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        // Create the update link with the product ID as a query parameter
        echo "<td><a class='updateLink' href='update_product.php?id=" . $row['id'] . "'>Update</a></td>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['album'] . "</td>";
        echo "<td>" . $row['artist'] . "</td>";
        echo "<td>" . $row['regular_price'] . "</td>";
        echo "<td>" . $row['media-condition'] . "</td>";
        echo "<td>" . $row['sleeve/insert condition'] . "</td>";
        echo "<td>" . $row['Pressing Year'] . "</td>";
        echo "<td>" . $row['format'] . "</td>";
        echo "<td>" . $row['stock'] . "</td>";
        // Fix this button's functionality.
        echo "<td><button class='updateLink' href='update_product.php?id=" . $row['id'] . "'>Update</button></td>";
        // Display more columns for other product details
    

        echo "</tr>";
      }

      // Close the connection
      mysqli_close($con);
      ?>
    </table>

  </body>

  </html>

<?php } ?>