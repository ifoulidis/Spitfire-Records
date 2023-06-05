<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <!-- Fonts -->
  <link
    href="https://fonts.googleapis.com/css?family=Handlee&Courgette&Bruno+Ace&Space+Grotesk:wght@400;700&Montserrat:400,700%7CRoboto"
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
  ?>
  <header>
    <div>
      <!-- Mobile slide-out menu -->
      <div id="mySidenav" class="sidenav">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <div>
          <a href="vinyl.php"><strong>Vinyl</strong></a>
        </div>
        <div class="dropdownItem">
          <a href="by_format.php"><strong>Music by format</strong> </a>
        </div>
        <div class="dropdownItem">
          <a href="by_genre.php"><strong>Music by genre</strong></a>
        </div>
        <div>
          <a href="about.php"><strong>About</strong></a>
        </div>
        <div>
          <a href="info.php"><strong>Info</strong></a>
        </div>
      </div>

      <!-- Mobile title and button -->
      <div class="menu">
        <div style="cursor:pointer" onclick="openNav()" id="sideNavToggle" class="menu__left">
          <p class="menu__bars">
            <i class="fa-solid fa-bars fa-xl"></i>
          </p>
        </div>
        <a href="index.php" class="logo">
          <img src="./images/logo.png" width=120 height=90 alt="Spitfire Records logo">
          <div>
            <h1 class="menu__logo">Spitfire Records</h1>
          </div>
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
        function openNav() {
          if ($(window).width() > 1200) {
            document.getElementById("mySidenav").style.width = "300px";
          }
          else if ($(window).width() <= 1200 && $(window).width() > 560) {
            document.getElementById("mySidenav").style.width = "50%";
          }
          else {
            document.getElementById("mySidenav").style.width = "100%";
          }
        }

        function closeNav() {
          document.getElementById("mySidenav").style.width = "0";
        }

      </script>
  </header>