<?php
// Not working
session_start();
if (!isset($_SESSION['admin_email'])) {

  echo "<script>window.open('log_in.php','_self')</script>";

} else {

  ?>
  <!DOCTYPE html>
  <html>

  <head>
    <title>Add Product</title>
    <link href="css/update.css" rel="stylesheet">
    <link href="css/admin_style.css" rel="stylesheet">
  </head>

  <body>
    <a href="./index.php" class="back-button"><i class="fa-solid fa-arrow-left"></i> Back</a>
    <div class="mainTitle">
      <h1>Add Product</h1>
    </div>

    <?php

    $con = mysqli_connect("localhost", "spitfire_ezzierara", "mC75KFzdcAEEjmV*&", "spitfire_db_the_first");
    if ($con->connect_error) {
      echo "<script>console.log('Debug Objects: " . $con->connect_error . "' );</script>";
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

      // Retrieve form data
      $album = htmlspecialchars($_POST['album']);
      $artist = $_POST['artist'];
      $year = intval($_POST['year']);
      $genre1 = $_POST['genre1'];
      $genre2 = $_POST['genre2'];
      $genre3 = $_POST['genre3'];
      $description = htmlspecialchars($_POST['description']);
      $regular_price = $_POST['regular_price'];
      $regular_price = filter_var($regular_price, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
      $new_used = intval($_POST['new_used']);
      $media_condition = $_POST['media_condition'];
      $sleeve_condition = $_POST['sleeve_condition'];
      $video_link = $_POST['video_link'];
      $track_listing = $_POST['track_listing'];
      $format = $_POST['format'];
      $number_of_discs = intval($_POST['number_of_discs']);
      $pressing_year = intval($_POST['pressing_year']);
      $pressing_country = $_POST['pressing_country'];
      $record_label = $_POST['record_label'];
      $stock = intval($_POST['stock']);

      // Sanitize input values
  
      $album = mysqli_real_escape_string($con, $album);
      $artist = mysqli_real_escape_string($con, $artist);
      $genre1 = mysqli_real_escape_string($con, $genre1);
      $genre2 = mysqli_real_escape_string($con, $genre2);
      $genre3 = mysqli_real_escape_string($con, $genre3);
      $description = mysqli_real_escape_string($con, $description);
      $media_condition = mysqli_real_escape_string($con, $media_condition);
      $sleeve_condition = mysqli_real_escape_string($con, $sleeve_condition);
      $video_link = mysqli_real_escape_string($con, $video_link);
      $track_listing = mysqli_real_escape_string($con, $track_listing);
      $format = mysqli_real_escape_string($con, $format);
      $pressing_country = mysqli_real_escape_string($con, $pressing_country);
      $record_label = mysqli_real_escape_string($con, $record_label);

      // Preparing the update query based on number of images
      if (!empty($_FILES['large_image']['tmp_name']) && !empty($_FILES['small_image']['tmp_name'])) {
        $image1 = $_FILES['large_image']['name'];
        $imageData1 = mysqli_real_escape_string($con, file_get_contents($_FILES['large_image']['tmp_name']));
        $image2 = $_FILES['small_image']['name'];
        $imageData2 = mysqli_real_escape_string($con, file_get_contents($_FILES['small_image']['tmp_name']));

        $query = "INSERT INTO products (album, artist, `year`, genre1, genre2, genre3, large_image, small_image, `description`, regular_price, `new/used`, `media-condition`, `sleeve/insert condition`, video_link, track_listing, format, `number_of_discs/records`, `Pressing Year`, `Pressing Country`, `Record Label`, stock) VALUES ('$album', '$artist', $year, '$genre1', '$genre2', '$genre3', '$imageData1', '$imageData2', '$description', $regular_price, $new_used, '$media_condition', '$sleeve_condition', '$video_link', '$track_listing', '$format', $number_of_discs, $pressing_year, '$pressing_country', '$record_label', $stock)";

      } else if (!empty($_FILES['large_image']['tmp_name']) || !empty($_FILES['small_image']['tmp_name'])) {
        echo "Warning! You cannot only insert one image!";
      } else {
        $query = "INSERT INTO products (album, artist, `year`, genre1, genre2, genre3, `description`, regular_price, `new/used`, `media-condition`, `sleeve/insert condition`, video_link, track_listing, format, `number_of_discs/records`, `Pressing Year`, `Pressing Country`, `Record Label`, stock) VALUES ('$album', '$artist', $year, '$genre1', '$genre2', '$genre3', '$description', $regular_price, $new_used, '$media_condition', '$sleeve_condition', '$video_link', '$track_listing', '$format', $number_of_discs, $pressing_year, '$pressing_country', '$record_label', $stock)";
      }

      // Execute the query
      $result = mysqli_query($con, $query);
      if ($result) {
        echo "<h2>Product added successfully.</h2>";
      } else {
        echo "<h2>Error creating product: " . mysqli_error($con) . " <h2>";
      }

    }
    // Retrieve the product ID from the query parameter
  
    // Retrieve the product information from the database
  
    // Display the update form with pre-filled values
    ?>
    <form class='updateForm' action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">

      <!-- Add input fields for other product details here -->

      <div class="row">
        <div class="col-25">
          <label for="album">Album:</label>
        </div>
        <div class="col-75">
          <input type="text" name="album" required><br><br>
        </div>
      </div>

      <div class="row">
        <div class="col-25">
          <label for="artist">Artist:</label>
        </div>
        <div class="col-75">
          <input type="text" name="artist" required><br><br>
        </div>
      </div>

      <div class="row">
        <div class="col-25">
          <label for="year">Year:</label>
        </div>
        <div class="col-75">
          <input type="text" name="year" required><br><br>
        </div>
      </div>

      <div class="row">
        <div class="col-25">
          <label for="genre1">Genre 1:</label>
        </div>
        <div class="col-75">
          <input type="text" name="genre1" required><br><br>
        </div>
      </div>

      <div class="row">
        <div class="col-25">
          <label for="genre2">Genre 2:</label>
        </div>
        <div class="col-75">
          <input type="text" name="genre2"><br><br>
        </div>
      </div>

      <div class="row">
        <div class="col-25">
          <label for="genre3">Genre 3:</label>
        </div>
        <div class="col-75">
          <input type="text" name="genre3"><br><br>
        </div>
      </div>
      <div class="row">
        <div class="col-25">
          <label for="image">Large Image</label>
        </div>
        <div class="col-75">
          <input type="file" name="large_image" accept="image/*"><br><br>
        </div>
      </div>
      <div class="row">
        <div class="col-25">
          <label for="image">Small Image</label>
        </div>
        <div class="col-75">
          <input type="file" name="small_image" accept="image/*"><br><br>
        </div>
      </div>

      <div class="row">
        <div class="col-25">
          <label for="description">Description:</label>
        </div>
        <div class="col-75">
          <textarea id='description' name="description"></textarea><br><br>
        </div>
      </div>

      <div class="row">
        <div class="col-25">
          <label for="regular_price">Regular Price:</label>
        </div>
        <div class="col-75">
          <input type="text" name="regular_price" required><br><br>
        </div>
      </div>

      <div class="row">
        <div class="col-25">
          <label for="new_used">New/Used (0=new, 1=used):</label>
        </div>
        <div class="col-75">
          <input type="text" name="new_used" required><br><br>
        </div>
      </div>

      <div class="row">
        <div class="col-25">
          <label for="media_condition">Media Condition:</label>
        </div>
        <div class="col-75">
          <input type="text" name="media_condition" required><br><br>
        </div>
      </div>

      <div class="row">
        <div class="col-25">
          <label for="sleeve_condition">Sleeve/Insert Condition:</label>
        </div>
        <div class="col-75">
          <input type="text" name="sleeve_condition" required><br><br>
        </div>
      </div>

      <div class="row">
        <div class="col-25">
          <label for="video_link">Video Link:</label>
        </div>
        <div class="col-75">
          <input type="text" name="video_link"><br><br>
        </div>
      </div>

      <div class="row">
        <div class="col-25">
          <label for="track_listing">Track Listing:</label>
        </div>
        <div class="col-75">
          <input type="text" name="track_listing"><br><br>
        </div>
      </div>

      <div class="row">
        <div class="col-25">
          <label for="format">Format:</label>
        </div>
        <div class="col-75">
          <select name="format" required>
            <option value="">Select Format</option>
            <option value="Vinyl LP">Vinyl LP</option>
            <option value="CD">CD</option>
            <option value="Music DVD">Music DVD</option>
            <option value="7 Inch Vinyl">7 Inch Vinyl</option>
            <option value="Cassette">Cassette</option>
          </select>
          <br><br>
        </div>
      </div>


      <div class="row">
        <div class="col-25">
          <label for="number_of_discs">No. of Discs/Records:</label>
        </div>
        <div class="col-75">
          <input type="text" name="number_of_discs" required><br><br>
        </div>
      </div>

      <div class="row">
        <div class="col-25">
          <label for="pressing_year">Pressing Year:</label>
        </div>
        <div class="col-75">
          <input type="text" name="pressing_year"><br><br>
        </div>
      </div>

      <div class="row">
        <div class="col-25">
          <label for="pressing_country">Pressing Country:</label>
        </div>
        <div class="col-75">
          <input type="text" name="pressing_country"><br><br>
        </div>
      </div>

      <div class="row">
        <div class="col-25">
          <label for="record_label">Record Label:</label>
        </div>
        <div class="col-75">
          <input type="text" name="record_label"><br><br>
        </div>
      </div>

      <div class="row">
        <div class="col-25">
          <label for="stock">Stock:</label>
        </div>
        <div class="col-75">
          <input type="text" name="stock" required><br><br>
        </div>
      </div>

      <input type="submit" value="Add">
    </form>

  </body>

  </html>

<?php } ?>