<form class='updateForm' action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
  <input type=" hidden" name="id" value="<?php echo $row['id']; ?>">
  <div class="row">
    <div class="col-25">
      <label for="image">Front Image</label>
    </div>
    <div class="col-75">
      <input type="file" name="front_image" accept="image/*"><br><br>
    </div>
  </div>