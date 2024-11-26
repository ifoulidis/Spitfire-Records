<?php
// Make image uploading work.
session_start();
if (!isset($_SESSION['admin_email'])) {

  echo "<script>window.open('log_in.php','_self')</script>";

} else {
  ?>
  <!DOCTYPE html>
  <html>

  <head>

    <title>Update Product</title>
    <link href="css/update.css" rel="stylesheet">
    <link href="css/admin_style.css" rel="stylesheet">
  </head>

  <body>
    <a href="<?php echo $_GET['return'] ?>" class="back-button"><i class="fa-solid fa-arrow-left"></i> Back</a>
    <h2>Update Product</h2>

    <?php

    $con = mysqli_connect("localhost", "spitfire_ezzierara", "mC75KFzdcAEEjmV*&", "spitfire_db_the_first");

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      // Retrieve form data
      $id = $_POST['id'];
      $return = $_POST['return_url'];
      $album = htmlspecialchars($_POST['album']);
      $artist = $_POST['artist'];
      $year = $_POST['year'];
      $genre1 = $_POST['genre1'];
      $genre2 = $_POST['genre2'];
      $genre3 = $_POST['genre3'];
      $description = htmlspecialchars($_POST['description']);
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

      // Sanitize input values
      $album = mysqli_real_escape_string($con, $album);
      $artist = mysqli_real_escape_string($con, $artist);
      $genre1 = mysqli_real_escape_string($con, $genre1);
      $genre2 = mysqli_real_escape_string($con, $genre2);
      $genre3 = mysqli_real_escape_string($con, $genre3);
      $description = mysqli_real_escape_string($con, $description);
      $new_used = mysqli_real_escape_string($con, $new_used);
      $media_condition = mysqli_real_escape_string($con, $media_condition);
      $sleeve_condition = mysqli_real_escape_string($con, $sleeve_condition);
      $video_link = mysqli_real_escape_string($con, $video_link);
      $track_listing = mysqli_real_escape_string($con, $track_listing);
      $format = mysqli_real_escape_string($con, $format);
      $pressing_country = mysqli_real_escape_string($con, $pressing_country);
      $record_label = mysqli_real_escape_string($con, $record_label);

      // Retrieve other form data here
      $target_dir = "C:\xampp\tmp"; // Change in production
      $query = "UPDATE products SET album='$album', artist='$artist', `year`=$year, genre1='$genre1', genre2='$genre2', genre3='$genre3', `description`='$description', regular_price=$regular_price, `new/used`='$new_used', `media-condition`='$media_condition', `sleeve/insert condition`='$sleeve_condition', video_link='$video_link', track_listing='$track_listing', format='$format', `number_of_discs/records`=$number_of_discs, `Pressing Year`=$pressing_year, `Pressing Country`='$pressing_country', `Record Label`='$record_label', stock=$stock WHERE id=$id";

      // Preparing the update query based on number of images
      if (!empty($_FILES['large_image']['tmp_name']) and !empty($_FILES['small_image']['tmp_name'])) {
        $image1 = $_FILES['large_image']['name'];
        $imageData1 = file_get_contents($_FILES['large_image']['tmp_name']);
        $image2 = $_FILES['small_image']['name'];
        $imageData2 = file_get_contents($_FILES['small_image']['tmp_name']);
        $query = "UPDATE products SET album=?, artist=?, `year`=?, genre1=?, genre2=?, genre3=?, large_image=?, small_image=?, `description`=?, regular_price=?, `new/used`=?, `media-condition`=?, `sleeve/insert condition`=?, video_link=?, track_listing=?, format=?, `number_of_discs/records`=?, `Pressing Year`=?, `Pressing Country`=?, `Record Label`=?, stock=? WHERE id=?";
        $stmt = mysqli_prepare($con, $query);

        // Binding the parameters
        mysqli_stmt_bind_param($stmt, 'ssissssssdisssssiissii', $album, $artist, $year, $genre1, $genre2, $genre3, $imageData1, $imageData2, $description, $regular_price, $new_used, $media_condition, $sleeve_condition, $video_link, $track_listing, $format, $number_of_discs, $pressing_year, $pressing_country, $record_label, $stock, $id);
      } elseif (!empty($_FILES['large_image']['tmp_name'])) {

        $image = $_FILES['large_image']['name'];
        $imageData = file_get_contents($_FILES['large_image']['tmp_name']);
        $query = "UPDATE products SET album=?, artist=?, `year`=?, genre1=?, genre2=?, genre3=?, large_image=?, `description`=?, regular_price=?, `new/used`=?, `media-condition`=?, `sleeve/insert condition`=?, video_link=?, track_listing=?, format=?, `number_of_discs/records`=?, `Pressing Year`=?, `Pressing Country`=?, `Record Label`=?, stock=? WHERE id=?";
        $stmt = mysqli_prepare($con, $query);

        // Binding the parameters
        mysqli_stmt_bind_param($stmt, 'ssisssssdisssssiissii', $album, $artist, $year, $genre1, $genre2, $genre3, $imageData, $description, $regular_price, $new_used, $media_condition, $sleeve_condition, $video_link, $track_listing, $format, $number_of_discs, $pressing_year, $pressing_country, $record_label, $stock, $id);
      } else {
        $query = "UPDATE products SET album=?, artist=?, `year`=?, genre1=?, genre2=?, genre3=?, `description`=?, regular_price=?, `new/used`=?, `media-condition`=?, `sleeve/insert condition`=?, video_link=?, track_listing=?, format=?, `number_of_discs/records`=?, `Pressing Year`=?, `Pressing Country`=?, `Record Label`=?, stock=? WHERE id=?";
        $stmt = mysqli_prepare($con, $query);

        // Binding the parameters
        mysqli_stmt_bind_param($stmt, 'ssissssdisssssiissii', $album, $artist, $year, $genre1, $genre2, $genre3, $description, $regular_price, $new_used, $media_condition, $sleeve_condition, $video_link, $track_listing, $format, $number_of_discs, $pressing_year, $pressing_country, $record_label, $stock, $id);
      }

      // Execute the update statement
      if (mysqli_stmt_execute($stmt)) {
        echo "Product updated successfully.";
        echo "<a href='" . $return . "'>Return To Previous Page</a>";
      } else {
        echo "Error updating product: " . mysqli_error($con);
      }
    } else {
      $con = mysqli_connect("localhost", "spitfire_ezzierara", "mC75KFzdcAEEjmV*&", "spitfire_db_the_first");
      // Retrieve the product ID from the query parameter
      $id = $_GET['id'];
      $return_url = $_GET['return'];

      // Retrieve the product information from the database
      $query = "SELECT * FROM products WHERE id=?";
      $stmt = mysqli_prepare($con, $query);
      mysqli_stmt_bind_param($stmt, 'i', $id);
      mysqli_stmt_execute($stmt);
      $result = mysqli_stmt_get_result($stmt);
      $row = mysqli_fetch_assoc($result);

      // Display the update form with pre-filled values
      ?>
      <form class='updateForm' action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
        <!-- Add input fields for other product details here -->

        <div class="row">
          <div class="col-25">
            <label for="album">Album:</label>
          </div>
          <div class="col-75">
            <input type="text" name="album" value="<?php echo $row['album']; ?>" required><br><br>
          </div>
        </div>

        <div class="row">
          <div class="col-25">
            <label for="artist">Artist:</label>
          </div>
          <div class="col-75">
            <input type="text" name="artist" value="<?php echo $row['artist']; ?>" required><br><br>
          </div>
        </div>

        <div class="row">
          <div class="col-25">
            <label for="year">Year:</label>
          </div>
          <div class="col-75">
            <input type="text" name="year" value="<?php echo $row['year']; ?>" required><br><br>
          </div>
        </div>

        <div class="row">
          <div class="col-25">
            <label for="genre1">Genre 1:</label>
          </div>
          <div class="col-75">
            <input type="text" name="genre1" value="<?php echo $row['genre1']; ?>" required><br><br>
          </div>
        </div>

        <div class="row">
          <div class="col-25">
            <label for="genre2">Genre 2:</label>
          </div>
          <div class="col-75">
            <input type="text" name="genre2" value="<?php echo $row['genre2']; ?>"><br><br>
          </div>
        </div>

        <div class="row">
          <div class="col-25">
            <label for="genre3">Genre 3:</label>
          </div>
          <div class="col-75">
            <input type="text" name="genre3" value="<?php echo $row['genre3']; ?>"><br><br>
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
            <textarea id='description' name="description" required><?php echo $row['description']; ?></textarea><br><br>
          </div>
        </div>

        <div class="row">
          <div class="col-25">
            <label for="regular_price">Regular Price:</label>
          </div>
          <div class="col-75">
            <input type="text" name="regular_price" value="<?php echo $row['regular_price']; ?>" required><br><br>
          </div>
        </div>

        <div class="row">
          <div class="col-25">
            <label for="new_used">New/Used:</label>
          </div>
          <div class="col-75">
            <input type="text" name="new_used" value="<?php echo $row['new/used']; ?>" required><br><br>
          </div>
        </div>

        <div class="row">
          <div class="col-25">
            <label for="media_condition">Media Condition:</label>
          </div>
          <div class="col-75">
            <input type="text" name="media_condition" value="<?php echo $row['media-condition']; ?>" required><br><br>
          </div>
        </div>

        <div class="row">
          <div class="col-25">
            <label for="sleeve_condition">Sleeve/Insert Condition:</label>
          </div>
          <div class="col-75">
            <input type="text" name="sleeve_condition" value="<?php echo $row['sleeve/insert condition']; ?>"
              required><br><br>
          </div>
        </div>

        <div class="row">
          <div class="col-25">
            <label for="video_link">Video Link:</label>
          </div>
          <div class="col-75">
            <input type="text" name="video_link" value="<?php echo $row['video_link']; ?>"><br><br>
          </div>
        </div>

        <div class="row">
          <div class="col-25">
            <label for="track_listing">Track Listing:</label>
          </div>
          <div class="col-75">
            <input type="text" name="track_listing" value="<?php echo $row['track_listing']; ?>"><br><br>
          </div>
        </div>

        <div class="row">
          <div class="col-25">
            <label for="format">Format:</label>
          </div>
          <div class="col-75">
            <select name="format" required>
              <option value="">Select Format</option>
              <option value="Vinyl LP" <?php if ($row['format'] == 'Vinyl LP')
                echo 'selected'; ?>>Vinyl LP</option>
              <option value="CD" <?php if ($row['format'] == 'CD')
                echo 'selected'; ?>>CD</option>
              <option value="Music DVD" <?php if ($row['format'] == 'Music DVD')
                echo 'selected'; ?>>Music DVD</option>
              <option value="7 Inch Vinyl" <?php if ($row['format'] == '7 Inch Vinyl')
                echo 'selected'; ?>>7" Vinyl</option>
              <option value="Cassette" <?php if ($row['format'] == 'Cassette')
                echo 'selected'; ?>>Cassette</option>
            </select>
            <br><br>
          </div>
        </div>

        <div class="row">
          <div class="col-25">
            <label for="number_of_discs">No. of Discs/Records:</label>
          </div>
          <div class="col-75">
            <input type="text" name="number_of_discs" value="<?php echo $row['number_of_discs/records']; ?>"
              required><br><br>
          </div>
        </div>

        <div class="row">
          <div class="col-25">
            <label for="pressing_year">Pressing Year:</label>
          </div>
          <div class="col-75">
            <input type="text" name="pressing_year" value="<?php echo $row['Pressing Year']; ?>"><br><br>
          </div>
        </div>

        <div class="row">
          <div class="col-25">
            <label for="pressing_country">Pressing Country:</label>
          </div>
          <div class="col-75">
            <input type="text" name="pressing_country" value="<?php echo $row['Pressing Country']; ?>"><br><br>
          </div>
        </div>

        <div class="row">
          <div class="col-25">
            <label for="record_label">Record Label:</label>
          </div>
          <div class="col-75">
            <input type="text" name="record_label" value="<?php echo $row['Record Label']; ?>"><br><br>
          </div>
        </div>

        <div class="row">
          <div class="col-25">
            <label for="stock">Stock:</label>
          </div>
          <div class="col-75">
            <input type="text" name="stock" value="<?php echo $row['stock']; ?>" required><br><br>
          </div>
        </div>

        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
        <input type="hidden" name="return_url" value="<?php echo $return_url; ?>">

        <input type="submit" value="Update">
      </form>
      <?php

      // Close the statement
      mysqli_stmt_close($stmt);

      // Close the connection
      mysqli_close($con);
    }
    ?>

  </body>

  </html>

<?php } ?>