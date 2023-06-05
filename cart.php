<?php
session_start();
include("includes/db.php");
include("includes/header.php");
?>


<div id="content">
  <div class="cart__container">
    <div class="column" id="cart">
      <div class="box">
        <form action="cart.php" method="post" enctype="multipart/form-data">
          <h1>Shopping Cart</h1>

          <?php
          $ip_add = getRealUserIp();
          $select_cart = "SELECT * FROM cart WHERE ip_add='$ip_add'";
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
              $only_price = $row_cart['p_price'];
              $get_products = "SELECT * FROM products WHERE id='$pro_id'";
              $run_products = mysqli_query($con, $get_products);

              while ($row_products = mysqli_fetch_array($run_products)) {
                $product_title = $row_products['album'] . ", " . $row_products['artist'];
                $product_img1 = $encodedImage = base64_encode($row_products['front_image']);
                $pro_stock = $row_products['stock'];
                $sub_total = $only_price * $pro_qty;
                $_SESSION['products'][] = array("title" => $product_title, "quantity" => $pro_qty);
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
                        <?php for ($i = 1; $i <= $pro_stock; $i++) {
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
          $remove_id = $_POST['remove'];
          $delete_product = "DELETE FROM cart WHERE p_id='$remove_id'";
          $run_delete = mysqli_query($con, $delete_product);
          if ($run_delete) {
            echo "<script>window.open('cart.php','_self')</script>";
          }
        }
      }

      echo @$up_cart = update_cart();
      ?>
    </div>
    <div class="subtotal-container">
      <h2>Subtotal: $
        <?php echo $total; ?>
      </h2>
      <p class="text-muted">
        Urban Shipping: $9.99 for up to 4 items.
      </p>
      <p class="text-muted">
        Rural Shipping: $12.99 for up to 4 items.
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
          <a class="btn btn-success" href="order.php">Continue to Checkout</a>
        </div>
      </div>
    </div>
  </div>
</div>


<?php
include("includes/footer.php");
?>
<script src="js/bootstrap.min.js"></script>
<script>
  $(document).ready(function (data) {
    $('.quantitySelector').on('change', function () {
      var optionSelected = $(this).find('option:selected');
      var quantity = $(this).val();
      var productId = optionSelected.data('product_id');
      if (quantity != '') {
        $.ajax({
          url: "change.php",
          method: "POST",
          data: { id: productId, quantity: quantity },
          success: function (data) {
            $("body").load('cart.php');
          }
        });
      }
    });
  });
</script>
</body>

</html>