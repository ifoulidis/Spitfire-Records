<?php

$con = mysqli_connect("localhost", "root", "", "spitfire records");

//get the csv
$csvFile = file_get_contents('../data/data.csv');
//separate each line
$csv = explode("\n", $csvFile);

$x = 0;

foreach ($csv as $csvLine) {
  if ($x == 0) {
    $x++;
    continue;
  } else {
    $x++;
    //separate each field
    $data = explode(",", $csvLine);
    foreach ($data as $x => $value) {
      $data[$x] = str_replace('"', "", $value);
      $data[$x] = str_replace("'", "", $value);
    }
    $delete_product = "delete from products where album='$data[0]' AND artist='$data[1]'";
    $run_delete = mysqli_query($con, $delete_product);
    echo "1<br>";
  }

}



?>