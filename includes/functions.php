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

  //  Search string filter
  $query_portion = ' ';
  if ($searchstring != "") {
    $strings = explode(' ', $searchstring);
    foreach ($strings as $value) {
      $query_portion = ' AND (';
      // Check the album, artist, and format for a match to each part of the search string
      $query_portion .= "album LIKE '%$value%' OR artist LIKE '%$value%' OR format LIKE '$value%' ";
      $query_portion .= ') ';
    }
  }
  //  Genre filter
  if ($genre != 'all') {
    $query_portion .= " AND (genre1 = '$genre' OR  genre2 = '$genre' OR  genre3 = '$genre') ";
  }
  //  Format filter
  if ($format == '12" Vinyl') {
    // There is an issue with losing the full '12" Vinyl' format (leaving just 12) when importing data.
    $query_portion .= " AND NOT format = 'CD' ";
  } elseif ($format == 'CD') {
    $query_portion .= " AND format = '$format' ";
  }

  $searchQuery = "SELECT *
                  FROM products
                  WHERE stock > 0" .
    $query_portion .
    "ORDER BY id
                  LIMIT 15 
                  OFFSET $offset
                  ";


  $searchResults = mysqli_query($db, $searchQuery);
  if ($searchResults) {

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
                  <h3 class='new_used'>$new_used</h3>
                </div>
              </div>
            </a>
            <div class='product__bottom'>
              <div class='product__priceWrapper'>
                <h1><small>$</small><strong> " . $row_products['regular_price'] . "</strong></h1>
              </div>
              <!-- Add styles to this container -->
              <div class='product__formatContainer'>";
        if (strtolower($row_products['format']) == "12\" vinyl") {
          echo "<p>
                  <i class='fa-solid fa-record-vinyl fa-xl'></i>

                </p>
                <h1>12\" Vinyl</h1>";
        } elseif (strtolower($row_products['format']) == "cd") {
          echo "<p>
                  <span class='fa-solid fa-compact-disc fa-xl'></span>
                </p>
                <h1>CD</h1>";
        } else {
          echo "<p>
                  <span class='fa-solid fa-record-vinyl fa-xl'></span>

                </p>
                <h1>12\" Vinyl</h1>";

        }
        echo "</div>

              <form method='POST'> 
                <button class='cart' type='button' id='" . $pro_url . "' name='add_cart'>
                  <i class='fa fa-shopping-cart fa-l'></i> Add to Cart
                </button>
              </form>
            </div>
            
          </div>";
      }
    }
  } else {
    $errorMessage = mysqli_error($db);
    echo "Error executing query: $errorMessage";
  }
}

if (isset($_POST['action'])) {
  $search_content = '';
  $genre_selected = 'all';
  $format_selected = "all";
  $offset_selected = 0;
  if ($_POST['action'] == 'getProducts') {
    if (isset($_POST['search_query'])) {
      $search_content = $_POST['search_query'];
    }
    if (isset($_POST['genre_option'])) {
      $genre_selected = $_POST['genre_option'];
    }
    if (isset($_POST['format_option'])) {
      $format_selected = $_POST['format_option'];
    }
    if (isset($_POST['offset_increment'])) {
      $offset_selected = $_POST['offset_increment'];
    }
    getProducts($searchstring = $search_content, $genre = $genre_selected, $format = $format_selected, $offset = $offset_selected);
    $_POST['search_query'] = null;
    $_POST['genre_option'] = null;
    $_POST['format_option'] = null;
    $_POST['offset_increment'] = null;
  }
}



?>