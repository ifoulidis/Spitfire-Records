<?php
// Add delete product functionality
// Add search functionality
// Add pagination
session_start();
if (!isset($_SESSION['admin_email'])) {
  echo "<script>window.open('log_in.php','_self')</script>";
} else {
  $con = mysqli_connect("localhost", "root", "", "spitfire records");
  $limit = 50; // Number of records per page

  // Get the total number of products
  $total_query = "SELECT COUNT(*) as total FROM products";
  $total_result = mysqli_query($con, $total_query);
  $total_row = mysqli_fetch_assoc($total_result);
  $total_products = $total_row['total'];

  // Calculate the total number of pages
  $total_pages = ceil($total_products / $limit);

  // Get the current page number from the URL query parameter
  $current_page = isset($_GET['page']) ? $_GET['page'] : 1;

  // Calculate the offset for the SQL query
  $offset = ($current_page - 1) * $limit;

  // Retrieve products with pagination
  $query = "SELECT * FROM products LIMIT $limit OFFSET $offset";
  $result = mysqli_query($con, $query);

  ?>

  <!DOCTYPE html>
  <html>

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

    <!-- Pagination links -->
    <div class="pagination">
      <?php
      // Generate pagination links
      for ($page = 1; $page <= $total_pages; $page++) {
        echo "<a href='update_products.php?page=$page'>$page</a>";
      }
      ?>
    </div>

  </body>

  </html>

<?php } ?>