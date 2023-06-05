<?php

$con = mysqli_connect("localhost", "root", "", "spitfire records");

$query = "INSERT INTO `products`(`album`, `artist`, `year`, `genre1`, `genre2`, `genre3`, `description`, `regular_price`, `new/used`, `media-condition`, `sleeve/insert condition`, `video_link`, `track_listing`, `format`, `number_of_discs/records`, `Pressing Year`, `Pressing Country`, `Record Label`, `stock`)";
$query .= " VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($con, $query);

// Path to the downloaded CSV file
$csvFilePath = '../data/data.csv';

// Read the CSV file into an array
$rows = array_map('str_getcsv', file($csvFilePath));

// Get the column headers (first row of the CSV)
$headers = array_shift($rows);

// Add null strings depending on the number of genres
foreach ($rows as $row) {
  $genres = explode(', ', $row[3]);
  if (count($genres) == 1) {
    $genres[] = null;
    $genres[] = null;
  } elseif (count($genres) == 2) {
    $genres[] = null;
  }

  // Clean data
  foreach ($row as $x => $value) {
    $row[$x] = str_replace('"', "", $row[$x]);
    $row[$x] = str_replace("'", "", $row[$x]);
  }
  $row[5] = str_replace('$', "", $row[5]);

  // Convert types
  $row[2] = intval($row[2]);
  $row[5] = floatval($row[5]);
  $row[12] = floatval($row[12]);
  $row[13] = floatval($row[13]);
  $row[16] = floatval($row[16]);


  echo var_dump($row);
  echo "<br>";

  $params = [
    $row[0],
    $row[1],
    $row[2],
    $genres[0],
    $genres[1] ?? 'null',
    $genres[2] ?? 'null',
    $row[4],
    $row[5],
    $row[6],
    $row[7],
    $row[8],
    $row[9] ?? 'null',
    $row[10] ?? 'null',
    $row[11] ?? 'null',
    $row[12] ?? 'null',
    $row[13] ?? 'null',
    $row[14] ?? 'null',
    $row[15] ?? 'null',
    $row[16]
  ];
  // Bind the parameters
  mysqli_stmt_bind_param($stmt, 'ssissssdisssssiissi', ...$params);

  // Execute the prepared statement
  mysqli_stmt_execute($stmt);

  // Check if the statement was successfully executed
  if (mysqli_stmt_affected_rows($stmt) > 0) {
    echo "Inserted " . $row[1] . ", " . $row[2] . PHP_EOL;
  } else {
    echo "Error inserting data" . PHP_EOL;
  }
}

// Close the statement
mysqli_stmt_close($stmt);



?>