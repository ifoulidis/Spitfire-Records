<?php
$db = mysqli_connect("localhost", "root", "", "spitfire_records");
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

function getRandomProducts($number_of_results = 8){
  global $db;

  // Generates a random number at least 9 away 
  $count_query = "SELECT COUNT(*) AS cnt FROM products 
  WHERE genre1 = 'Hard Rock' OR  genre2 = 'Hard Rock' OR  genre3 = 'Hard Rock' or
  genre1 = 'Alternative' OR  genre2 = 'Alternative' OR  genre3 = 'Alternative' or
  genre1 = 'Metal' OR  genre2 = 'Metal' OR  genre3 = 'Metal' or
  genre1 = 'Classic Rock' OR  genre2 = 'Classic Rock' OR  genre3 = 'Classic Rock'
  ";
  $count_result = mysqli_query($db, $count_query);
  $count_row = mysqli_fetch_assoc($count_result);
  $num_of_rows = $count_row['cnt'];

  $x = 0;
  $rd_array = [];

  while ($x < $number_of_results * 10) {
      $random_num = rand(2177, 2177 + ($num_of_rows - $number_of_results));

      if (!in_array($random_num, $rd_array)) {
          $rd_array[] = $random_num;
          $x++;
      }
  }
  
  $inClause = implode(',', $rd_array);

  // Gets random results
  $searchQuery = "SELECT *
                FROM products
                WHERE stock > 0
                AND id IN ($inClause) LIMIT $number_of_results";



  $searchResults = mysqli_query($db, $searchQuery);
  if ($searchResults) {

    while ($row_products = mysqli_fetch_array($searchResults)) {

      $pro_url = $row_products['id'];
      if ($row_products['new/used'] == 0) {
        $new_used = "NEW";
      } else
        $new_used = "USED";
      $encodedImage = base64_encode($row_products['small_image']);
      // If there is no stock of a product, it is not shown. However, it can still be accessed via the product URL if the customer still has it.
      if ($row_products['stock'] != 0) {
        echo "<div class='product' >
            <a href='$pro_url'>
              <div class='product__top'>
                <div class='product__image'>
                  <img src='data:image/jpeg;base64,$encodedImage' alt='cover'>
                </div>
                <div class='product__info'>

                    <h1>" . stripslashes($row_products['album']) . "</h1>


                    <h3>" . stripslashes($row_products['artist']) . "</h3>
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
        switch (strtolower($row_products['format'])) {
          case "vinyl lp":
            echo "<p>
                  <i class='fa-solid fa-record-vinyl fa-xl'></i>
                </p>
                <h2>Vinyl LP</h2>";
            break;
          case "cd":
            echo "<p>
                  <span class='fa-solid fa-compact-disc fa-xl'></span
                </p>
                <h2>CD</h2>";
            break;
          case "music dvd":
            echo "<p>
                  <span class='fa-solid fa-compact-disc fa-xl'></span>
                </p>
                <h2>Music DVD</h2>";
            break;
          case '7 inch vinyl':
            echo "<p>
                  <span class='fa-solid fa-record-vinyl fa-xl'></span>
                </p>
                <h2>7&quot; Vinyl</h2>";
            break;
          case 'cassette':
            echo "<p>
                  <span class='fa-solid fa-cassette-vhs fa-xl'></span>
                </p>
                <h2>Cassette</h2>";
            break;
          default:
            echo "<p>
                  <i class='fa-solid fa-record-vinyl fa-xl'></i>
                </p>
                <h2>Vinyl LP</h2>";
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

function getProducts($searchstring = "", $genre = "all", $format = "all", $condition = "all", $order = "default", $offset = 0)
{
  global $db;

  $searchstring = mysqli_real_escape_string($db, $searchstring);
  $genre = mysqli_real_escape_string($db, $genre);
  $format = mysqli_real_escape_string($db, $format);
  $condition = mysqli_real_escape_string($db, $condition);
  $order = mysqli_real_escape_string($db, $order);
  $offset = mysqli_real_escape_string($db, $offset);

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
  // Condition filter
  if ($condition != "all") {
    if ($condition == 0) {
      $query_portion .= " AND `new/used` = 0 ";
    } else {
      $query_portion .= " AND `new/used` = 1 ";
    }
  }
  //  Format filter
  if ($format != "all") {
    $query_portion .= " AND format = '$format' ";
  }
  // Order by
  $order_str = "IF(album LIKE '%$searchstring%', 0, 1), 
              IF(artist LIKE '%$searchstring%', 0, 1)";
  if (isset($order)) {
    switch ($order) {
      case "price_high_low":
        $order_str .= ", regular_price DESC";
        break;
      case "price_low_high":
        $order_str .= ", regular_price";
        break;
      case "album_a_z":
        $order_str .= ", album";
        break;
      case "album_z_a":
        $order_str .= ", album DESC";
        break;
      case "artist_a_z":
        $order_str .= ", artist";
        break;
      case "artist_z_a":
        $order_str .= ", artist DESC";
        break;
      default:
        $order_str .= ", artist, album";
    }
  }


  $searchQuery = "SELECT *
                  FROM products
                  WHERE stock > 0" .
    $query_portion .
    "ORDER BY " .
    $order_str .
    " LIMIT 16 
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
      $encodedImage = base64_encode($row_products['small_image']);
      // If there is no stock of a product, it is not shown. However, it can still be accessed via the product URL if the customer still has it.
      if ($row_products['stock'] != 0) {
        echo "<div class='product' >
            <a href='$pro_url'>
              <div class='product__top'>
                <div class='product__image'>
                  <img src='data:image/jpeg;base64,$encodedImage' alt='cover'>
                </div>
                <div class='product__info'>

                    <h1>" . stripslashes($row_products['album']) . "</h1>


                    <h3>" . stripslashes($row_products['artist']) . "</h3>
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
        switch (strtolower($row_products['format'])) {
          case "vinyl lp":
            echo "<p>
                  <i class='fa-solid fa-record-vinyl fa-xl'></i>
                </p>
                <h2>Vinyl LP</h2>";
            break;
          case "cd":
            echo "<p>
                  <span class='fa-solid fa-compact-disc fa-xl'></span
                </p>
                <h2>CD</h2>";
            break;
          case "music dvd":
            echo "<p>
                  <span class='fa-solid fa-compact-disc fa-xl'></span>
                </p>
                <h2>Music DVD</h2>";
            break;
          case '7 inch vinyl':
            echo "<p>
                  <span class='fa-solid fa-record-vinyl fa-xl'></span>
                </p>
                <h2>7&quot; Vinyl</h2>";
            break;
          case 'cassette':
            echo "<p>
                  <span class='fa-solid fa-music fa-xl'></span>
                </p>
                <h2>Cassette</h2>";
            break;
          default:
            echo "<p>
                  <i class='fa-solid fa-music fa-xl'></i>
                </p>
                <h2>Vinyl LP</h2>";
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

function getLatest($genre = "all", $format = "all", $condition = "all", $order = "default", $offset = 0)
{
  global $db;

  $genre = mysqli_real_escape_string($db, $genre);
  $format = mysqli_real_escape_string($db, $format);
  $condition = mysqli_real_escape_string($db, $condition);
  $order = mysqli_real_escape_string($db, $order);
  $offset = mysqli_real_escape_string($db, $offset);

  $query_portion = ' ';

  //  Genre filter
  if ($genre != 'all') {
    $query_portion .= "AND (genre1 = '$genre' OR  genre2 = '$genre' OR  genre3 = '$genre') ";
  }
  // Condition filter
  if ($condition != "all") {
    if ($condition == 0) {
      $query_portion .= "AND `new/used` = 0 ";
    } else {
      $query_portion .= "AND `new/used` = 1 ";
    }
  }
  //  Format filter
  if ($format != "all") {
    $query_portion .= "AND format = '$format' ";
  }
  // Order by
  $order_str = '';
  if ($order != 'default') {
    switch ($order) {
      case "price_high_low":
        $order_str = "ORDER BY regular_price DESC";
        break;
      case "price_low_high":
        $order_str = "ORDER BY regular_price";
        break;
      case "album_a_z":
        $order_str = "ORDER BY album";
        break;
      case "album_z_a":
        $order_str = "ORDER BY album DESC";
        break;
      case "artist_a_z":
        $order_str = "ORDER BY artist";
        break;
      case "artist_z_a":
        $order_str = "ORDER BY artist DESC";
        break;
      default:
        $order_str = "ORDER BY artist, album";
    }
  }


  $searchQuery = "SELECT * FROM `products` WHERE stock > 0 
AND DATEDIFF(CURDATE(), created) <= 14" .
    $query_portion .
    $order_str .
    " LIMIT 16 
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
      $encodedImage = base64_encode($row_products['small_image']);
      // If there is no stock of a product, it is not shown. However, it can still be accessed via the product URL if the customer still has it.
      if ($row_products['stock'] != 0) {
        echo "<div class='product' >
            <a href='$pro_url'>
              <div class='product__top'>
                <div class='product__image'>
                  <img src='data:image/jpeg;base64,$encodedImage' alt='cover'>
                </div>
                <div class='product__info'>

                    <h1>" . stripslashes($row_products['album']) . "</h1>


                    <h3>" . stripslashes($row_products['artist']) . "</h3>
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
        switch (strtolower($row_products['format'])) {
          case "vinyl lp":
            echo "<p>
                  <i class='fa-solid fa-record-vinyl fa-xl'></i>
                </p>
                <h2>Vinyl LP</h2>";
            break;
          case "cd":
            echo "<p>
                  <span class='fa-solid fa-compact-disc fa-xl'></span
                </p>
                <h2>CD</h2>";
            break;
          case "music dvd":
            echo "<p>
                  <span class='fa-solid fa-compact-disc fa-xl'></span>
                </p>
                <h2>Music DVD</h2>";
            break;
          case '7 inch vinyl':
            echo "<p>
                  <span class='fa-solid fa-record-vinyl fa-xl'></span>
                </p>
                <h2>7&quot; Vinyl</h2>";
            break;
          case 'cassette':
            echo "<p>
                  <span class='fa-solid fa-solid fa-cassette-vhs fa-xl'></span>
                </p>
                <h2>Cassette</h2>";
            break;
          default:
            echo "<p>
                  <i class='fa-solid fa-record-vinyl fa-xl'></i>
                </p>
                <h2>Vinyl LP</h2>";
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
  $condition_selected = "all";
  $order_selected = "default";
  $offset_selected = 0;
  if ($_POST['action'] == 'getProducts' or $_POST['action'] == 'getLatestProducts') {
    if (isset($_POST['search_query'])) {
      $search_content = $_POST['search_query'];
    }
    if (isset($_POST['genre_option'])) {
      $genre_selected = $_POST['genre_option'];
    }
    if (isset($_POST['format_option'])) {
      $format_selected = $_POST['format_option'];
    }
    if (isset($_POST['condition'])) {
      $condition_selected = $_POST['condition'];
    }
    if (isset($_POST['orderby'])) {
      $order_selected = $_POST['orderby'];
    }
    if (isset($_POST['offset_increment'])) {
      $offset_selected = $_POST['offset_increment'];
    }
    if ($_POST['action'] == 'getProducts') {
      getProducts($searchstring = $search_content, $genre = $genre_selected, $format = $format_selected, $condition = $condition_selected, $order = $order_selected, $offset = $offset_selected);
    } else if ($_POST['action'] == 'getLatestProducts') {
      getLatest($genre = $genre_selected, $format = $format_selected, $condition = $condition_selected, $order = $order_selected, $offset = $offset_selected);
    }
    $_POST['search_query'] = null;
    $_POST['genre_option'] = null;
    $_POST['format_option'] = null;
    $_POST['condition'] = null;
    $_POST['offset_increment'] = null;
  }
  elseif ($_POST['action'] == 'getRandomProducts'){
    getRandomProducts($number_of_results = $_POST['num_results']);
  }
}



?>