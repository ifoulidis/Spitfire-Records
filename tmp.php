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

          <div class="table-responsive">
            <table class="cart-table">
              <thead>
                <tr>
                  <th colspan="2">Product</th>
                  <th for="quantitySelector">Quantity</th>
                  <th>Unit Price</th>
                  <th colspan="1">Select</th>
                  <th colspan="2">Sub Total</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $total = 0;
                while ($row_cart = mysqli_fetch_array($run_cart)) {
                  $pro_id = $row_cart['p_id'];
                  $pro_qty = $row_cart['qty'];
                  $only_price = $row_cart['p_price'];
                  $get_products = "SELECT * FROM products WHERE id='$pro_id'";
                  $run_products = mysqli_query($con, $get_products);
                  $increment = 0;
                  while ($row_products = mysqli_fetch_array($run_products)) {
                    $product_title = $row_products['album'] . ", " . $row_products['artist'];
                    $product_img1 = $encodedImage = base64_encode($row_products['front_image']);
                    $pro_stock = $row_products['stock'];
                    $sub_total = $only_price * $pro_qty;
                    $_SESSION['products'][$increment] = array("title" => $product_title, "quantity" => $pro_qty);
                    $increment++;
                    $total += $sub_total;
                    ?>
                    <tr>
                      <td>
                        <img width="40" height="40" src='data:image/jpeg;base64,<?php echo $encodedImage; ?>'>
                      </td>
                      <td>
                        <a href="#">
                          <?php echo $product_title; ?>
                        </a>
                      </td>
                      <td>
                        <select name="quant" id="quantitySelector">
                          <?php for ($i = 1; $i <= $pro_stock; $i++) {
                            if ($i == $pro_qty) {
                              echo "<option selected value='$i' data-product_id='$pro_id' class='quantity'>$i</option>";
                            } else {
                              echo "<option value='$i' data-product_id='$pro_id' class='quantity'>$i</option>";
                            }
                          } ?>
                        </select>
                      </td>
                      <td>
                        $
                        <?php echo $only_price; ?>
                      </td>
                      <td>
                        <input type="checkbox" name="remove[]" value="<?php echo $pro_id; ?>">
                      </td>
                      <td>
                        $
                        <?php echo $sub_total; ?>
                      </td>
                    </tr>
                  <?php }
                }
                $_SESSION['price'] = $total * 100;
                ?>
              </tbody>
              <tfoot>
                <tr>
                  <th colspan="5">Total (Before Shipping)</th>
                  <th colspan="2">$
                    <?php echo $total; ?>
                  </th>
                </tr>
              </tfoot>
            </table>
          </div>

          <div class="box-footer">
            <div class="pull-left">
              <a href="index.php" class="btn btn-default">
                <i class="fa fa-chevron-left"></i> Continue Shopping
              </a>
            </div>
            <div class="pull-right">
              <button class="btn btn-info" type="submit" name="update" value="Update Cart">
                <i class="fa fa-refresh"></i> Delete Selection
              </button>
            </div>
          </div>
        </form>
        <a class="btn btn-success" href="order.php">
          Continue to Checkout
        </a>
      </div>

      <?php
      function update_cart()
      {
        global $con;
        if (isset($_POST['update'])) {
          foreach ($_POST['remove'] as $remove_id) {
            $delete_product = "DELETE FROM cart WHERE p_id='$remove_id'";
            $run_delete = mysqli_query($con, $delete_product);
            if ($run_delete) {
              echo "<script>window.open('cart.php','_self')</script>";
            }
          }
        }
      }
      echo @$up_cart = update_cart();
      ?>
    </div>
  </div>
</div>



<?php

include("includes/footer.php");

?>

<script src="js/bootstrap.min.js"></script>


<script>

  $(document).ready(function (data) {

    $('select').on('change', function () {
      // Note the following syntax for finding and getting the data - it was the only way that worked!
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