<?php
session_start();
include("includes/header.php");
include("includes/db.php");

$genreSearch = "SELECT DISTINCT genre1 FROM products WHERE stock > 0
UNION SELECT DISTINCT genre2 FROM products WHERE stock > 0
UNION SELECT DISTINCT genre3 FROM products WHERE stock > 0";
global $db;
$searchResults = mysqli_query($db, $genreSearch);
$list = [];
?>

<main>
  <!-- Discount banner -->
  <?php
    // 30% discount on used CDs
    $currentDateTime = new DateTime();
    $startDateTime = new DateTime('2024-11-27 00:00:00'); // Wednesday, November 11, 2024, 12:00 AM
    $endDateTime = new DateTime('2024-12-01 23:59:59');   // Sunday, December 1, 2024, 11:59 PM
    if ($currentDateTime >= $startDateTime && $currentDateTime <= $endDateTime){
      echo '  <div class="sale__banner">
      <h1>30% Off Used CDs</h1>
      <p>Ends Midnight Sunday</p>
      </div>
      <br>'; 
    }
  ?>

  <div class="cardBox">
    <div class="home__card" id="card__shopRecords">
      <a href="./filter.php?format=Vinyl%20LP">
        <h1>Shop Vinyl</h1>
      </a>
    </div>
    <div class="home__card" id="card__shopCDs">
      <a href="./filter.php?format=CD">
        <h1>Shop CDs</h1>
      </a>
    </div>
    <div class="home__card" id="card__shopMore">
      <a href="./filter.php">
        <h1>Shop All</h1>
      </a>
    </div>
  </div>
  <hr class="home_hr" style="box-shadow: 0 3px 3px black;">
  <div class="home__container">
    <!-- Filter Modal -->
    <div  class="featured">
      <h1>Featured Products</h1>
      <div class="products_cont">
        <!-- Loading bars -->
        <div class="bars-1"></div>
      </div>
      <a id="seeAll" href="./filter.php?">See All</a>
    </div>
  </div>
</main>

<?php include("includes/footer.php"); ?>
</body>

<script>
  $(document).ready(function () {
    // Create a media query
    var mediaQuery1 = window.matchMedia("(max-width: 1280px)");
    var mediaQuery2 = window.matchMedia("(max-width: 776px)");
    var num_results = 12;

    // Check if the media query matches
    if (mediaQuery2.matches) {
        // The viewport width is 600px or less
        num_results = 4;
    } else if (mediaQuery1.matches) {
        // The viewport width is greater than 600px
        num_results = 8;
    }

    function getRandomProducts() {
      data = {
        'action': 'getRandomProducts',
        'num_results': Number(num_results)
      };
      console.log(data);
      $('.bars-1').show();
      $.post('includes/functions.php', data, function (response) {
        if (response) {
          $('html, body').animate({ scrollTop: '0px' }, 300);
          $('.bars-1').hide();
          $(".products_cont").html(response);
        } else {
          if (offset > 0) {
          } else {

            $(".products_cont").html("<p style='text-align:center;'>No results found!</p>");
          }
        }
      });
    }

    // Retrieve products
    getRandomProducts();


    // Clicking on the Add To Cart button
    // Binding it to .products_cont' allows both functions to work.
    $('.products_cont').on('click', '.cart', function (e) {
      e.preventDefault(); // Prevent the default form submission
      // Function for the adding to cart effect
      var cart = $('.menu__cart');
      var imgtodrag = $(this).parents('.product').find("img").eq(0);
      if (imgtodrag) {
        var imgclone = imgtodrag.clone()
          .offset({
            top: imgtodrag.offset().top,
            left: imgtodrag.offset().left
          })
          .css({
            'opacity': '0.5',
            'position': 'absolute',
            'height': '150px',
            'width': '150px',
            'z-index': '100'
          })
          .appendTo($('body'))
          .animate({
            'top': cart[0].getBoundingClientRect().top + 10,
            'left': cart[0].getBoundingClientRect().left + 10,
            'width': 75,
            'height': 75
          }, 1000, 'easeInOutExpo');

        setTimeout(function () {
          cart.effect("shake", {
            times: 2
          }, 200);
        }, 1500);

        imgclone.animate({
          'width': 0,
          'height': 0
        }, function () {
          $(this).detach()
        });
      }
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
  });
</script>

</html>