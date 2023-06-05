<?php

$db = mysqli_connect("localhost", "root", "", "spitfire records");

function getRealUserIp()
{
  switch (true) {
    case (!empty($_SERVER['HTTP_X_REAL_IP'])):
      return $_SERVER['HTTP_X_REAL_IP'];
    case (!empty($_SERVER['HTTP_CLIENT_IP'])):
      return $_SERVER['HTTP_CLIENT_IP'];
    case (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])):
      return $_SERVER['HTTP_X_FORWARDED_FOR'];
    default:
      return $_SERVER['REMOTE_ADDR'];
  }
}

function cartCount()
{
  global $db;

  $ip_add = getRealUserIp();

  $select_cart = "select * from cart where ip_add='$ip_add'";

  $run_cart = mysqli_query($db, $select_cart);

  echo mysqli_num_rows($run_cart);
}


function getProducts($searchstring = "", $genre = "all", $format = "all", $offset = 0)
{
  global $db;
  //Add use of search string.

  if ($genre == "all" and $format = "all") {
    $searchQuery = "SELECT *
FROM products
WHERE stock > 0
ORDER BY id
LIMIT 15 
OFFSET $offset
";
  } elseif ($genre == "all" and $format != "all") {
    $searchQuery = "SELECT * FROM products WHERE format = $format and  stock > 0 LIMIT 15 OFFSET $offset ORDER BY id";
  } elseif ($genre != "all" and $format == "all") {
    $searchQuery = "SELECT * FROM products WHERE $genre IN (genre1, genre2, genre3) and stock > 0 LIMIT 15 OFFSET $offset ORDER BY id";
  } else {
    $searchQuery = "SELECT * FROM products where stock >0 LIMIT 15 OFFSET $offset ORDER BY id";
  }

  $searchResults = mysqli_query($db, $searchQuery);

  while ($row_products = mysqli_fetch_array($searchResults)) {

    $pro_url = $row_products['id'];
    if ($row_products['new/used'] == 0) {
      $new_used = "NEW";
    } else
      $new_used = "USED";
    $encodedImage = base64_encode($row_products['front_image']);
    // If there is no stock of a product, it is not shown. However, it can still be accessed via the product URL if the customer still has it.
    if ($row_products['stock'] != 0) {
      echo "<div class='product' >
            <a href='$pro_url'>
              <div class='product__top'>
                <img class='product__image' src='data:image/jpeg;base64,$encodedImage' alt='cover'>
                <div class='product__info'>
                  <h1>" . $row_products['album'] . "</h1>
                  <h3>" . $row_products['artist'] . "</h3>
                  <h3>$new_used</h3>
                </div>
              </div>
            </a>
            <div class='product__bottom'>
              <div class='product__priceWrapper'>
                <h2><small>$</small><strong> " . $row_products['regular_price'] . "</strong></h2>
              </div>
              <!-- Add styles to this container -->
              <div class='product__formatContainer'>";
      if (strtolower($row_products['format']) == "12\" vinyl") {
        echo "<p>
                  <span class='fa-solid fa-record-vinyl fa-xl'></span>

                </p>
                <h3>12\" Vinyl</h3>";
      } elseif (strtolower($row_products['format']) == "cd") {
        echo "<p>
                  <span class='fa-solid fa-compact-disc fa-xl'></span>
                </p>
                <h3>CD</h3>";
      } else {
        echo "<p>
                  <span class='fa-solid fa-record-vinyl fa-xl'></span>

                </p>
                <h3>12\" Vinyl</h3>";

      }
      echo "</div>

              <form method='POST'> 
                <button class='cart' type='button' id='" . $pro_url . "' name='add_cart'>
                  <i class='fa fa-shopping-cart fa-xl'></i> Add to Cart
                </button>
              </form>
            </div>
            
          </div>";
    }
  }
}

if (isset($_POST['action'])) {
  if ($_POST['action'] == 'getProducts') {
    if (isset($_POST['offset_increment'])) {
      getProducts("", "all", "all", intval($_POST['offset_increment'])); // Pass the value without assignment
      echo "<script>console.log(" . intval($_POST['offset_increment']) . ")</script>";
    } else {
      getProducts(); // No need to pass the $offset parameter here
    }
  } elseif ($_POST['action'] == 'filterProducts') {
    // Remember to unset these by a user clicking 'reset'.
    if (isset($_POST['genre_option']) && isset($_POST['format']) && isset($_POST['offset'])) {
      getProducts($_POST['searchQuery'], $_POST['genre_option'], $_POST['format'], $_POST['offset']);
    } else {
      echo "<script>alert('There was a problem searching./nPlease reload and try again')";
    }
  }
}


?>