<?php
// Add search functionality.
// Add ability to see album and artist (just add fields on all relevant pages).
// Add desktop and mobile styles.

session_start();
if (!isset($_SESSION['admin_email'])) {
  echo "<script>window.open('log_in.php','_self')</script>";
} else {
  $con = mysqli_connect("localhost", "root", "", "spitfire records");
  $limit = 50; // Number of records per page

  // Get the total number of products
  $total_query = "SELECT COUNT(*) as total FROM orders ORDER BY 'date'";
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
  $query = "SELECT * FROM orders ORDER BY 'DATE' LIMIT $limit OFFSET $offset";
  $result = mysqli_query($con, $query);

  ?>

  <!DOCTYPE html>
  <html>

  <head>
    <title>Update Products</title>
    <link href="css/admin_style.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  </head>

  <body>
    <h2>Update Products</h2>
    <h3>Leave fields blank if they need to be empty</h3>
    <table>
      <tr>
        <th>Date</th>
        <th>Product ID</th>
        <th>Qty</th>
        <th>Fulfillment Status</th>
        <th>Customer Name</th>
        <th>Street</th>
        <th>Town</th>
        <th>Zip Code</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Invoice No.</th>
        <th>Order Status</th>
        <th>Actions</th>
      </tr>

      <?php
      // Display the products in a table
      while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        // Create the update link with the product ID as a query parameter
        echo "<td>" . $row['date'] . "</td>";
        echo "<td>" . $row['product_id'] . "</td>";
        echo "<td>" . $row['qty'] . "</td>";
        echo "<td>" . $row['fulfillment_status'] . "</td>";
        echo "<td>" . $row['customer_name'] . "</td>";
        echo "<td>" . $row['street'] . "</td>";
        echo "<td>" . $row['town'] . "</td>";
        echo "<td>" . $row['zip'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td>" . $row['phone'] . "</td>";
        echo "<td>" . $row['invoice_no'] . "</td>";
        echo "<td>" . $row['order_status'] . "</td>";
        // A button to change fulfillment_status to 'complete' for each row
        if ($row['order_status'] == "pending") {
          echo "<td><button class='markComplete' data-productid='" . $row['id'] . "'>Mark Complete</button></td>";
        } else {
          echo "<td><button class='markComplete' data-productid='" . $row['id'] . "'>Undo</button></td>";
        }
        echo "</tr>";
      }

      // Close the connection
      mysqli_close($con);
      ?>

      <script>
        $(document).ready(function () { // Attach event handler to the Mark Complete buttons         
          $('.markComplete').click(function () {
            var button = $(this);
            var productId = $(this).data('productid');
            var status = $(this).contents();
            var completion = "incomplete";
            if (status == "Undo") {
              completion = "complete";
            }
            var url = "functions/update_fulfillment.php?id=" + productId + "&completion=" + completion;
            // AJAX request to update the fulfillment status           
            $.ajax({
              url: url, type: 'GET', success: function (response) {  // Reload the page after successful update               
                button.text(response);
              },
              error: function (xhr, status, error) {  // Handle error case              
                console.log(error);
              }
            });
          });
        });
      </script>

    </table>

    <!-- Pagination links -->
    <div class="pagination">
      <?php
      // Generate pagination links
      for ($page = 1; $page <= $total_pages; $page++) {
        echo "<a href='orders.php?page=$page'>$page</a>";
      }
      ?>
    </div>

  </body>

  </html>

<?php } ?>