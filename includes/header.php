<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <!-- Fonts -->
  <link
    href="https://fonts.googleapis.com/css?family=Handlee|Roboto:wght@100,400|Courgette|Bruno+Ace|New+Rocker|Space+Grotesk:400,700|Montserrat:400,700|Roboto&display=swap"
    rel="stylesheet">
  <meta http-equiv="x-ua-compatible" content="IE=edge, chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- J-Query -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
  <!-- Font Awesome -->
  <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet">

  <title>Spitfire Records</title>
  <link href="styles/header.css" rel="stylesheet">
  <link href="styles/style.css" rel="stylesheet">
  <link href="styles/product.css" rel="stylesheet">
  <link href="styles/cart.css" rel="stylesheet">
  <link href="styles/footer.css" rel="stylesheet">

  <script src="https://kit.fontawesome.com/edd72e6a34.js" crossorigin="anonymous"></script>
</head>

<body>
  <?php
  include("includes/functions.php");
  $searchQuery = "SELECT DISTINCT genre1 FROM products WHERE stock > 0
UNION SELECT DISTINCT genre2 FROM products WHERE stock > 0
UNION SELECT DISTINCT genre3 FROM products WHERE stock > 0";
  global $db;
  $searchResults = mysqli_query($db, $searchQuery);
  $list = [];
  ?>
  <header>
    <div>
      <!-- Mobile slide-out menu -->
      <div id="mySidenav" class="sidenav">
        <a href="javascript:void(0)" class="closebtn">&times;</a>
        <img src="./images/logo.png" width=150 height=120 id="pullOut_logo" alt="Spitfire Records logo">
        <div class="dropdownItem">
          <a href="#"><strong>Music by format</strong> <i class="fa-solid fa-chevron-down fa-sm"></i></a>
          <div class="dropdown-content">
            <a href="filter.php?format=CD">CDs</a>
            <a href="filter.php?format=12%22%20Vinyl">Vinyl</a>
          </div>
        </div>
        <div class="dropdownItem">
          <a id="#" href="#"><strong>Music by genre</strong> <i class="fa-solid fa-chevron-down fa-sm"></i></a>
          <div class="dropdown-content">
            <?php
            while ($sortedGenre = mysqli_fetch_array($searchResults)) {
              $genreValue = $sortedGenre[0];
              if ($genreValue !== 'null') {
                $list[] = $genreValue;
              }
            }
            sort($list);
            foreach ($list as $item) {
              if ($item !== 'null') {
                $url_key = rawurlencode($item);
                echo "<a href='filter.php?genre=$url_key'>$item</a>";
              }
            }
            ?>
          </div>
        </div>

        <div class="dropdownItem">
          <a href="about.php"><strong>About</strong></a>
        </div>
        <div class="dropdownItem">
          <a href="contact_us.php"><strong>Contact Us</strong></a>
        </div>
      </div>

      <!-- Mobile title and button -->
      <div class="menu">
        <div style="cursor:pointer" id="sideNavToggle" class="menu__left">
          <p class="menu__bars">
            <i class="fa-solid fa-bars fa-xl"></i>
          </p>
        </div>
        <a href="index.php" class="logo">
          <img src="./images/logo.png" width=150 height=120 id="main_logo" alt="Spitfire Records logo">
          <img src="./images/title.png" width=340.5 height=110 id="main_title" alt="Spitfire Records title">
        </a>
        <div class="menu__right">
          <p class='menu__cart'>
            <span class='fa-solid fa-cart-shopping fa-xl'></span>
          </p>
          <p id="cartCount">
            <?php cartCount() ?>
          </p>
        </div>
      </div>

      <script>
        $(document).ready(function () {
          $('.dropdownItem').click(function (e) {
            $(this).toggleClass('open');
          });
          $('.closebtn').click(function (e) {
            e.preventDefault();
            document.getElementById("mySidenav").style.width = "0";
            $('#overlay').remove();

            // Reset the z-index of the menu
            $('.sidenav').css('z-index', '1');
          })
          $('#sideNavToggle').click(function (e) {
            e.preventDefault();

            if ($(window).width() > 1200) {
              $('#pullOut_logo').hide();
              document.getElementById("mySidenav").style.width = "300px";

              // Add a dimming overlay to the body
              $('<div id="overlay"></div>').appendTo('body');

              // Make the menu visible by adjusting the z-index
              $('.sidenav').css('z-index', '9999');
            }
            else if ($(window).width() <= 1200 && $(window).width() > 560) {
              document.getElementById("mySidenav").style.width = "50%";
            }
            else {
              document.getElementById("mySidenav").style.width = "100%";
            }
          });
        });

      </script>
  </header>