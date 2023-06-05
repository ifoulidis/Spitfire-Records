<?php
session_start();
include("includes/header.php");
include("includes/db.php");
?>

<main>
  <div class="home__container">
    <form class="searchbar" method='POST'>
      <input type="search" placeholder="Search for artist or album..." name="searchQuery">
      <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
    </form>
    <div class="grid-container">
      <!-- Add custom fonts throughout -->
    </div>
    <div class="center">
      <div class="home__paginator">
        <button id="prevPage">❮</button>
        <button id="nextPage">❯</button>
      </div>
    </div>
  </div>
</main>

<?php
include("includes/footer.php");
?>

</body>

<script>
  $(document).ready(function () {
    initialData = { 'action': 'getProducts' };
    offset = 0;

    // Retrieve products
    $.post('includes/functions.php', initialData, function (response) {
      $(".grid-container").html(response);
      console.log(response.error);
    });

    $("#nextPage").click(function (e) {
      offset += 15;
      e.preventDefault();
      data = {
        'action': 'getProducts',
        'offset_increment': offset
      };
      $.post('includes/functions.php', data, function (response) {
        $(".grid-container").html(response);
        console.log(response.error);
      });
    });

    $("#prevPage").click(function (e) {
      e.preventDefault();
      offset -= 15;
      data = {
        'action': 'getProducts',
        'offset_increment': offset
      };
      $.post('includes/functions.php', data, function (response) {
        $(".grid-container").html(response);
        console.log(response.error);
      });
    });

    // Clicking on the Add To Cart button
    // Binding it to .grid-container' allows both functions to work.
    $('.grid-container').on('click', '.cart', function (e) {
      e.preventDefault(); // Prevent the default form submission

      var id = $(this).attr('id'); // Get the ID attribute of the button
      var data = {
        id: id
      };

      $.ajax({
        url: 'add_to_cart.php',
        method: 'POST',
        data: data,
        success: function (response) {
          console.log(response);

          // Check if the response contains an alert
          if (response.includes('alert(')) {
            // Create a new div for the alert
            var $alert = $('<div class="alert">' + response + '</div>');

            // Append the alert to the body
            $('body').append($alert);

            // Add a click event to close the alert
            $alert.on('click', function () {
              $(this).remove();
            });
          } else {
            // Update the #cartCount element
            $("#cartCount").html(response);
          }
        },
        error: function (xhr, status, error) {
          // Handle the error here
          console.log(xhr.responseText);
        }
      });
    });

    // Clicking on the cart icon in the menu.
    $('.menu__cart').click(function () {
      window.location.href = 'cart.php';
      return false;
    });
  });
</script>

</html>