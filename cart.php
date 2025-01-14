<?php
session_start();
include("includes/db.php");
include("includes/header.php");
?>

<main>
  <div id="content">
  <!-- Discount banner -->
  <?php
    // 50% discount on used compilation CDs
    $currentDateTime = new DateTime();
    $startDateTime = new DateTime('2024-11-27 00:00:00'); // Wednesday, November 11, 2024, 12:00 AM
    $endDateTime = new DateTime('2024-12-01 23:59:59');   // Sunday, December 1, 2024, 11:59 PM
    if ($currentDateTime >= $startDateTime && $currentDateTime <= $endDateTime){
      echo '  <div class="sale__banner">
      <h1>50% Off Used Compilation CDs</h1>
      <p>Ends Midnight Febraury 1st</p>
      </div>
      <br>'; 
    }
  ?>
    <div class="cart__container">
      <div class="column" id="cart">
        <div class="box">
          <form action="cart.php" method="post" enctype="multipart/form-data">
            <h1 class="cart__title">Shopping Cart</h1>

            <?php
            $ip_add = mysqli_real_escape_string($con, getRealUserIp());
            $out_of_stock = "no";
            // Only select the items present in products
            $select_cart = "SELECT c.* FROM cart c
                INNER JOIN products p ON c.p_id = p.id
                WHERE c.ip_add='$ip_add'";

            $run_cart = mysqli_query($con, $select_cart);
            $count = mysqli_num_rows($run_cart);
            ?>

            <p class="text-muted">
              You currently have
              <?php echo $count; ?> item(s) in your cart.
            </p>

            <div class="card-container">
              <?php
              $total = 0;
              while ($row_cart = mysqli_fetch_array($run_cart)) {
                $pro_id = $row_cart['p_id'];
                $pro_qty = $row_cart['qty'];

                $get_products = "SELECT * FROM products WHERE id='$pro_id'";
                $run_products = mysqli_query($con, $get_products);

                while ($row_products = mysqli_fetch_array($run_products)) {
                  $product_title = $row_products['album'] . ", " . $row_products['artist'];
                  $product_img1 = $encodedImage = base64_encode($row_products['large_image']);
                  $pro_stock = $row_products['stock'];
                  $only_price = $row_cart['p_price'];
                  if ($pro_stock <= 0) {
                    $out_of_stock = "yes";
                  }
                  
                  $_SESSION['products'][] = array("title" => $product_title, "quantity" => $pro_qty);
                  
                  // 50% discount on used compilation CDs
                  $currentDateTime = new DateTime();
                  $startDateTime = new DateTime('2025-01-15 00:00:00'); // Wednesday, January 15, 2025, 12:00 AM
                  $endDateTime = new DateTime('2024-01-31 23:59:59');   // Friday, January 31, 2025, 11:59 PM
                  $priceMultiplier = ($currentDateTime >= $startDateTime && $currentDateTime <= $endDateTime) ? 0.5 : 1;

                  if ($row_products['new/used'] == 1 and $row_products['format'] === "CD" and str_contains($product_found['artist'], 'Various')) {
                    $only_price = round($only_price * $priceMultiplier, 2);
                    $only_price = number_format($only_price, 2, '.', '');
                    $sub_total = round(($only_price * $pro_qty), 2);
                    $sub_total = number_format($sub_total, 2, '.', '');
                  }
                  else{
                    $sub_total = $only_price * $pro_qty;
                  }


                  $total += $sub_total;
                  ?>

                  <div class="card">
                    <div class="card-image">
                      <img src="data:image/jpeg;base64,<?php echo $encodedImage; ?>" alt="">
                    </div>
                    <div class="card-content">
                      <h4>
                        <?php echo $product_title; ?>
                      </h4>
                      <p>Price: $
                        <?php echo $only_price; ?>
                      </p>
                      <p>Quantity:
                        <select name="quant" class="quantitySelector" data-product_id="<?php echo $pro_id; ?>">
                          <?php
                          // Checks if the product is out of stock
                          if ($pro_stock <= 0) {

                            echo "<option style='color: red;' selected disabled>Out of Stock</option>";
                          }
                          // Creates options for quantity based on stock
                          for ($i = 1; $i <= $pro_stock; $i++) {
                            if ($i == $pro_qty) {
                              echo "<option selected value='$i'>$i</option>";
                            } else {
                              echo "<option value='$i'>$i</option>";
                            }
                          } ?>
                        </select>
                      </p>
                      <p>Sub Total: $
                        <?php echo $sub_total; ?>
                      </p>
                      <button type="submit" name="remove" value="<?php echo $pro_id; ?>" class="cart__deleteButton"
                        onclick="update_cart()">Delete</button>
                    </div>
                  </div>

                <?php }
              }
              $_SESSION['price'] = $total * 100;
              ?>
            </div>
          </form>
        </div>

        <?php
        function update_cart()
        {
          global $con;
          if (isset($_POST['remove'])) {
            $remove_id = mysqli_escape_string($con, $_POST['remove']);
            $delete_product = "DELETE FROM cart WHERE p_id='$remove_id'";
            $run_delete = mysqli_query($con, $delete_product);
            if ($run_delete) {
              echo "<script>window.open('cart.php','_self')</script>";
            }
          }
        }

        update_cart();
        ?>
      </div>
      <div class="subtotal-container">
        <h1>Before Shipping: $
          <?php echo $total; ?>
        </h1>
        <p class="text-muted">
          Vinyl - $12.50 Flat Rate Nationwide (Non Rural)
        </p>
        <p class="text-muted">
          Vinyl - $18.20 Flat Rate Nationwide (Rural)
        </p>
        <p class="text-muted">
          CDs - $6.50 Flat Rate Nationwide (Non Rural)
        </p>
        <p class="text-muted">
          CDs - $12.20 Flat Rate Nationwide (Rural)
        </p>
        <p class="text-muted">
          Free Pickup Near Matamata
        </p>
        <br>
        <br>
        <div class="continueShoppingButton">
          <div class="pull-left">
            <a href="index.php" class="btn btn-default">
              <i class="fa fa-chevron-left"></i> Continue Shopping
            </a>
          </div>
          <div class="checkout-btn">
            <?php
            if ($count === 0 or $out_of_stock === "yes") {
              echo "<script>console.log('" . $count . "')</script>";
              echo "<a href='#' class='btn btn-success' data-valid='no'>Continue to Checkout</a>";
            } else {
              echo "<a href='#' class='btn btn-success' data-valid='yes'>Continue to Checkout</a>";
            }
            ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<?php
include("includes/footer.php");
?>
<script src="js/bootstrap.min.js"></script>
<script>
  $(document).ready(function (data) {
    $('select[name="quant"]').on('change', function () {
      var quantity = $(this).val();
      var productId = $(this).data('product_id');
      console.log(productId)
      if (quantity != '') {
        $.ajax({
          url: "change.php",
          method: "post",
          data: { id: productId, quantity: quantity },
          success: function () {
            // Reload the page to reflect the change in price.
            location.reload();
          },
          error: function (xhr, status, error) {
            // Handle errors
            console.log(error);
          }
        });
      }
    });

    $('.btn-success').click(function (e) {
      e.preventDefault();
      var isValid = $(this).data('valid');
      if (isValid === 'yes') {
        // Navigate to order.php
        window.location.href = 'order.php';
      } else {
        alert("Have you got anything in your cart? Please check all the items in your cart are in stock.")
      }
    });
  });
</script>
</body>

</html>