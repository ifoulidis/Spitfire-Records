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

    <h2>Update Product</h2>

    <?php

    $con = mysqli_connect("localhost", "root", "", "spitfire records");

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      // Retrieve form data
      $id = $_POST['id'];
      $album = $_POST['album'];
      $artist = $_POST['artist'];
      $year = $_POST['year'];
      $genre1 = $_POST['genre1'];
      $genre2 = $_POST['genre2'];
      $genre3 = $_POST['genre3'];
      $front_image = $_POST['front_image'];
      $back_image = $_POST['front_image'];
      $description = $_POST['description'];
      $regular_price = $_POST['regular_price'];
      $new_used = $_POST['new_used'];
      $media_condition = $_POST['media_condition'];
      $sleeve_condition = $_POST['sleeve_condition'];
      $video_link = $_POST['video_link'];
      $track_listing = $_POST['track_listing'];
      $format = $_POST['format'];
      $number_of_discs = $_POST['number_of_discs'];
      $pressing_year = $_POST['pressing_year'];
      $pressing_country = $_POST['pressing_country'];
      $record_label = $_POST['record_label'];
      $stock = $_POST['stock'];

      // Retrieve other form data here
  
      // Preparing the update query based on number of images
      if (!empty($_FILES['front_image']['tmp_name']) and !empty($_FILES['back_image']['tmp_name'])) {
        $image1 = $_FILES['front_image']['name'];
        $imageData1 = file_get_contents($_FILES['front_image']['tmp_name']);
        $image2 = $_FILES['back_image']['name'];
        $imageData2 = file_get_contents($_FILES['back_image']['tmp_name']);
        $query = "INSERT INTO products (album, artist, year, genre1, genre2, genre3, front_image, back_image, description, regular_price, new/used, media-condition, sleeve/insert condition, video_link, track_listing, format, number_of_discs/records, Pressing Year, Pressing Country, Record Label, stock) VALUES(??????????????????????)";
        $stmt = mysqli_prepare($con, $query);

        // Binding the parameters
        mysqli_stmt_bind_param($stmt, 'ssisssbbssisssssiissii', $album, $artist, $year, $genre1, $genre2, $genre3, $imageData, $description, $regular_price, $new_used, $media_condition, $sleeve_condition, $video_link, $track_listing, $format, $number_of_discs, $pressing_year, $pressing_country, $record_label, $stock, $id);
      } elseif (!empty($_FILES['front_image']['tmp_name'])) {
        $image = $_FILES['front_image']['name'];
        $imageData = file_get_contents($_FILES['front_image']['tmp_name']);
        $query = "INSERT INTO products (album, artist, year, genre1, genre2, genre3, front_image, description, regular_price, new/used, media-condition, sleeve/insert condition, video_link, track_listing, format, number_of_discs/records, Pressing Year, Pressing Country, Record Label, stock) VALUES(?????????????????????)";
        $stmt = mysqli_prepare($con, $query);

        // Binding the parameters
        mysqli_stmt_bind_param($stmt, 'ssisssbssisssssiissii', $album, $artist, $year, $genre1, $genre2, $genre3, $imageData, $description, $regular_price, $new_used, $media_condition, $sleeve_condition, $video_link, $track_listing, $format, $number_of_discs, $pressing_year, $pressing_country, $record_label, $stock, $id);
      } else {
        $query = "INSERT INTO products (album, artist, year, genre1, genre2, genre3, description, regular_price, new/used, media-condition, sleeve/insert condition, video_link, track_listing, format, number_of_discs/records, Pressing Year, Pressing Country, Record Label, stock) VALUES(?????????????????????)";
        $stmt = mysqli_prepare($con, $query);

        // Binding the parameters
        mysqli_stmt_bind_param($stmt, 'ssissssdisssssiissii', $album, $artist, $year, $genre1, $genre2, $genre3, $description, $regular_price, $new_used, $media_condition, $sleeve_condition, $video_link, $track_listing, $format, $number_of_discs, $pressing_year, $pressing_country, $record_label, $stock, $id);
      }

      // Execute the update statement
      if (mysqli_stmt_execute($stmt)) {
        echo "Product updated successfully.";
        echo "<a href='update_products.php'>Return To All Products</a>";
      } else {
        echo "Error creating product: " . mysqli_error($con);
      }


      // Close the statement
      mysqli_stmt_close($stmt);

      // Close the connection
      mysqli_close($con);
    } else {
      // Retrieve the product ID from the query parameter
  
      // Retrieve the product information from the database
  
      // Display the update form with pre-filled values
      ?>
      <form class='updateForm' action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

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
            <label for="image">Front Image</label>
          </div>
          <div class="col-75">
            <input type="file" name="front_image" accept="image/*"><br><br>
          </div>
        </div>
        <div class="row">
          <div class="col-25">
            <label for="image">Back Image</label>
          </div>
          <div class="col-75">
            <input type="file" name="back_image" accept="image/*"><br><br>
          </div>
        </div>

        <div class="row">
          <div class="col-25">
            <label for="description">Description:</label>
          </div>
          <div class="col-75">
            <textarea id='description' name="description" required></textarea><br><br>
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
            <label for="new_used">New/Used:</label>
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
            <input type="text" name="video_link" required><br><br>
          </div>
        </div>

        <div class="row">
          <div class="col-25">
            <label for="track_listing">Track Listing:</label>
          </div>
          <div class="col-75">
            <input type="text" name="track_listing" required><br><br>
          </div>
        </div>

        <div class="row">
          <div class="col-25">
            <label for="format">Format:</label>
          </div>
          <div class="col-75">
            <input type="text" name="format" required><br><br>
          </div>
        </div>

        <div class="row">
          <div class="col-25">
            <label for="number_of_discs">Number of Discs/Records:</label>
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
            <input type="text" name="pressing_year" required><br><br>
          </div>
        </div>

        <div class="row">
          <div class="col-25">
            <label for="pressing_country">Pressing Country:</label>
          </div>
          <div class="col-75">
            <input type="text" name="pressing_country" required><br><br>
          </div>
        </div>

        <div class="row">
          <div class="col-25">
            <label for="record_label">Record Label:</label>
          </div>
          <div class="col-75">
            <input type="text" name="record_label" required><br><br>
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

        <input type="hidden" name="id">


        <input type="submit" value="Update">
      </form>
      <?php
    }
    ?>

  </body>

  </html>

<?php } ?>