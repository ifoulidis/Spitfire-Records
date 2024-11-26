<?php

$db = mysqli_connect("localhost", "root", "", "spitfire_records");


function getProducts($searchstring = "", $genre = "all", $format = "all", $condition = "all", $offset = 0)
{
  global $db;

  // URL to return (pagination)
  $return_url = $_POST['url'];
  //  Search string filter
  $query_portion = ' ';
  if ($searchstring != "") {
    $strings = explode(' ', $searchstring);
    foreach ($strings as $value) {
      $query_portion = ' AND (';
      $int = intval($value);
      // Check the album, artist, and format for a match to each part of the search string
      $query_portion .= "id LIKE '%$int%' or album LIKE '%$value%' OR artist LIKE '%$value%' ";
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

  $searchQuery = "SELECT *
    FROM products
    WHERE stock >= 0" .
    $query_portion .
    "ORDER BY IF(id LIKE '$searchstring%', 0, 1), 
              IF(album LIKE '%$searchstring%', 0, 1), 
              IF(artist LIKE '%$searchstring%', 0, 1), 
              artist,
              album
    LIMIT 15 
    OFFSET $offset";


  $searchResults = mysqli_query($db, $searchQuery);
  if ($searchResults) {
    echo "<tr>
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
      </tr>";
    while ($row_products = mysqli_fetch_array($searchResults)) {
      echo "<tr>";
      // Create the update link with the product ID as a query parameter
      echo "<td><button class='updateLink' data-productid='" . $row_products['id'] . "'>Update</button></td>";
      echo "<td>" . $row_products['id'] . "</td>";
      echo "<td>" . $row_products['album'] . "</td>";
      echo "<td>" . $row_products['artist'] . "</td>";
      echo "<td>" . $row_products['regular_price'] . "</td>";
      echo "<td>" . $row_products['media-condition'] . "</td>";
      echo "<td>" . $row_products['sleeve/insert condition'] . "</td>";
      echo "<td>" . $row_products['Pressing Year'] . "</td>";
      echo "<td>" . $row_products['format'] . "</td>";
      echo "<td>" . $row_products['stock'] . "</td>";
      // Fix this button's functionality.
      echo "<td><a class='deleteLink deleteProduct' href='#' data-productid='" . $row_products['id'] . "'>Delete</a></td>";
      // Display more columns for other product details
      echo "</tr>";
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
    if (isset($_POST['condition'])) {
      $condition_selected = $_POST['condition'];
    }
    if (isset($_POST['offset_increment'])) {
      $offset_selected = $_POST['offset_increment'];
    }
    getProducts($searchstring = $search_content, $genre = $genre_selected, $format = $format_selected, $condition = $condition_selected, $offset = $offset_selected);
    $_POST['search_query'] = null;
    $_POST['genre_option'] = null;
    $_POST['format_option'] = null;
    $_POST['condition'] = null;
    $_POST['offset_increment'] = null;
  }
}



?>