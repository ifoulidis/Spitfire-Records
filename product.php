<?php
include("includes/db.php");

$productID = $_GET["pro_id"];
global $con;


$searchQuery = "select * from products WHERE id=?";
$product_statement = mysqli_prepare($con, $searchQuery);
mysqli_stmt_bind_param($product_statement, 'i', $productID);

mysqli_stmt_execute($product_statement);
$searchResult = mysqli_stmt_get_result($product_statement);
$product = mysqli_fetch_assoc($searchResult);

// ###
// Get all the relevant properties of the product into usable variables.
$album = htmlspecialchars(stripslashes($product["album"]));
$artist = $product["artist"];
if (isset($product["year"])) {
  $year = $product["year"];
} else {
  $year = "None";
}
$price = $product["regular_price"];
// The image from the database needs to be converted.
$plainImage = $product['large_image'];
$encodedImage = base64_encode($product['large_image']);
if ($product["on_sale"] == 1) {
  $sale_price = $product["sale_price"];
}

if ($product["new/used"] == 0) {
  $media_condition = "Brand New";
  $insert_condition = "Brand New";
} elseif ($product["new/used"] == 1) {
  $media_condition = $product["media-condition"];
  $insert_condition = $product["sleeve/insert condition"];
}

$description = htmlspecialchars(stripslashes($product["description"]));

// Deal with video links so that they are embedded

function convertToEmbedUrl($url)
{
  if (strpos($url, 'embed') === false) {
    $parsedUrl = parse_url($url);
    parse_str($parsedUrl['query'], $query);

    if (isset($query['v'])) {
      $videoId = $query['v'];
      $embedUrl = "https://www.youtube.com/embed/$videoId";
      return $embedUrl;
    }
  }
  return $url; // Return the original URL if "embed" is already present or "v" parameter is not found
}

if (isset($product["video_link"]) and $product["video_link"] != "") {
  $video = $product["video_link"];
  $convertedUrl = convertToEmbedUrl($video);
}


$pro_stock = $product['stock'];

$genres = "";

// Set array or variable for genres
if ($product["genre1"] != "null") {
  $genres .= $product["genre1"];
}
if ($product["genre2"] != "null") {
  $genres .= ", " . $product["genre2"];
}
if ($product["genre3"] != "null") {
  $genres .= ", " . $product["genre3"];
}

// ###############
// The following attempts to generate an image for sharing, but the image gets stretched, so is not active.
// Get the dimensions of the original image
// $dimensions = getimagesizefromstring($plainImage);
// $originalWidth = $dimensions[0];
// $originalHeight = $dimensions[1];
// $targetWidth = 1200;
// $targetHeight = 630;
// $targetAspectRatio = 1.91 / 1; // Facebook sharing ratio of 1.91:1

// // Calculate the new dimensions without distorting the image (same as before)
// if ($originalWidth / $originalHeight > $targetAspectRatio) {
//   // Image is wider than the target ratio, calculate new height
//   $newWidth = $originalHeight * $targetAspectRatio;
//   $newHeight = $originalHeight;
// } else {
//   // Image is taller than the target ratio, calculate new width
//   $newWidth = $originalWidth;
//   $newHeight = $originalWidth / $targetAspectRatio;
// }

// // Create a new blank image with the target dimensions
// $newImage = imagecreatetruecolor($targetWidth, $targetHeight);
// $backgroundColor = imagecolorallocate($newImage, 255, 255, 255); // White background
// imagefill($newImage, 0, 0, $backgroundColor);

// // Calculate the position to paste the original image for letterboxing or pillarboxing

// $positionX = ($targetWidth - $newWidth) / 2;
// $positionY = ($targetHeight - $newHeight) / 2;

// // Copy and resize the original image to the new blank image with letterboxing or pillarboxing
// $originalImage = imagecreatefromstring($plainImage);
// imagecopyresampled($newImage, $originalImage, $positionX, $positionY, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);


// $imageFilePath = 'images/products/' . $productID . '.jpg';
// // Save the manipulated image as a file
// imagejpeg($newImage, $imageFilePath, 90); // Adjust the path and filename as needed

// // Get the URL of the saved image file
// $imageURL = 'https://spitfirerecords.co.nz/images/products/' . $productID . '.jpg'; // Replace this with the actual URL of the saved image

// // Free up space
// imagedestroy($originalImage);
// imagedestroy($newImage);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <!-- Meta info tags -->
  <meta property='og:title' content='<?php echo $album . ', ' . $artist . ' - Spitfire Records'; ?>' />

  <!-- Thumbnail image -->
  <meta property='og:image' content='https://spitfirerecords.co.nz/images/thumbnail_logo.jpeg' />
  <meta property='og:description'
    content='<?php $album . ', ' . $album . ' (' . $_POST['format'] . ')' . ' - ' . $price ?>' />
  <meta property='og:url' content='https://spitfirerecords.co.nz/<?php echo $_GET["pro_id"]; ?>' />
  <meta name="twitter:image" content="https://spitfirerecords.co.nz/images/thumbnail_logo.jpeg">
  <meta property="og:type" content='website' />
  <meta name="description" content='<?php $album . ', ' . $album . ' (' . $_POST['format'] . ')' . ' - ' . $price ?>' />
  <!-- Fonts -->
  <link
    href="https://fonts.googleapis.com/css?family=Handlee|Roboto:wght@100,400|Courgette|Bruno+Ace|New+Rocker|Space+Grotesk:400,700|Montserrat:400,700|Roboto&display=swap"
    rel="stylesheet">
  <meta http-equiv="x-ua-compatible" content="IE=edge, chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- J-Query -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <!-- Font Awesome -->
  <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet">

  <title>Spitfire Records</title>
  <link href="styles/header-v2.css" rel="stylesheet">
  <link href="styles/style-v2.css" rel="stylesheet">
  <link href="styles/product-v2.css" rel="stylesheet">
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
        <a href="index.php">
          <img src="./images/logo.png" width=150 height=120 id="pullOut_logo" alt="Spitfire Records logo">
        </a>
        <div class="dropdownItem">
          <a href="#"><strong>By Format</strong> <i class="fa-solid fa-chevron-down fa-sm"></i></a>
          <div class="dropdown-content">
            <a href="filter.php?format=CD">CDs</a>
            <a href=<?php echo "filter.php?format=" . urlencode("Vinyl LP") ?>>Vinyl</a>
            <a href=<?php echo "filter.php?format=" . urlencode("Music DVD") ?>>Music DVD</a>
            <a href=<?php echo "filter.php?format=" . urlencode('7 Inch Vinyl') ?>>7&quot; Vinyl</a>
            <a href=<?php echo "filter.php?format=" . urlencode("Cassette") ?>>Cassette</a>
          </div>
        </div>
        <div class="dropdownItem">
          <a id="#" href="#"><strong>By Genre</strong> <i class="fa-solid fa-chevron-down fa-sm"></i></a>
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
          <a href="#"><strong>By Condition</strong> <i class="fa-solid fa-chevron-down fa-sm"></i></a>
          <div class="dropdown-content">
            <a href="filter.php?condition=0">New</a>
            <a href="filter.php?condition=1">Used</a>
          </div>
        </div>

        <div class="dropdownItem">
          <a href="about.php"><strong>About</strong></a>
        </div>
        <div class="dropdownItem">
          <a href="contact_us.php"><strong>Contact Us</strong></a>
        </div>
        <br>
        <br>
      </div>

      <!-- Main Menu Bar -->
      <div class="menu">
        <div style="cursor:pointer" id="sideNavToggle" class="menu__left">
          <p class="menu__bars">
            <i class="fa-solid fa-bars fa-xl"></i>
          </p>
        </div>
        <a href="index.php" class="logo">
          <img src="./images/logo.png" width=150 height=120 id="main_logo" alt="Spitfire Records logo">
          <img src="./images/title.png" width=340.5 height=110 id="main_title" alt="Spitfire Records title">
          <img src="./images/fullLogo.png" id="mobile_logo" alt="Spitfire Records Mobile">
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
  </header>

  <!-- Product details HTML -->
  <main>
    <div class="productDetails__container">
      <div class="productDetails__top">
        <div class="productDetails__left">

          <img class="productDetails__image" src="data:image/jpeg;base64,<?php echo $encodedImage; ?>"
            alt="Album Cover">
          <div class="productDetails__price">
            <h1>$
              <?php echo $price; ?>
            </h1>
          </div>
          <?php if ($pro_stock > 0) {
            echo "
        <a href='#' class='productDetails__cartButton mobile' id='" . $productID . "'>
          <i class='fa fa-shopping-cart fa-xl' ></i>&nbsp;Add to Cart
        </a>";
          } else {
            echo "<button href='contact_us.php' class='productDetails__cartButton mobile'>
          Request Stock
        </button>";
          } ?>
        </div>
        <div class="productDetails__right">
          <div class="tab">
            <button class="tablinks" data-tab="Basic">Basic</button>
            <button class="tablinks" data-tab="Details">Details</button>
          </div>
          <div class="productDetails__tabcontent" id="Basic">

            <div class="productDetails__title">
              <h1>
                <?php echo $album; ?>
              </h1>
              <h2>
                <?php echo $artist; ?>
              </h2>
            </div>
            <div class="productDetails__line">
              <h2>Year:</h2>
              <p>
                <?php echo $year; ?>
              </p>
            </div>
            <div class="productDetails__line">
              <h2>Format:</h2>
              <p>
                <?php if ($product["format"] == "7 Inch Vinyl") {
                  echo "7&quot; Vinyl";
                } else {
                  echo $product["format"];
                } ?>
              </p>
            </div>
            <div class="productDetails__line">
              <h2>Media Condition:</h2>
              <div class="productDetails__line">
                <div style="float:left;">
                  <p>
                    <?php echo $media_condition; ?>
                  </p>
                </div>
                <div class="hintArea" id="hint1" style="float:right;">
                  <a href="https://www.goldminemag.com/collector-resources/record-grading-101" target="_blank"
                    rel="noopener noreferrer"><i class="fa-solid fa-circle-question" style="color: #1765ee;"></i></a>
                  <span class="discogsHintText" id="reveal1">Our ratings are based on the Goldmine Grading Guide</span>
                </div>
              </div>
            </div>
            <div class="productDetails__line">
              <h2>Sleve/Insert Condition:</h2>
              <div style="float:left;">
                <p>
                  <?php echo $insert_condition; ?>
                </p>
              </div>
              <div class="hintArea" id="hint2" style="float:right;">
                <a href="https://www.goldminemag.com/collector-resources/record-grading-101" target="_blank"
                  rel="noopener noreferrer"><i class="fa-solid fa-circle-question" style="color: #1765ee;"></i></a>
                <span class="discogsHintText" id="reveal2">Our ratings are based on the Goldmine Grading Guide</span>
              </div>

            </div>
            <div class="productDetails__line">
              <h2>Genres:</h2>
              <p class="productDetails__genres">
                <?php echo $genres; ?>
              </p>

            </div>
            <div class="productDetails__line">
              <h2>Description:</h2>

              <p class="productDetails__description">
                <?php echo $description; ?>
              </p>

            </div>
          </div>

          <div class="productDetails__tabcontent" id="Details">
            <div class="productDetails__specialLine">
              <h2>Track Listing:</h2>

              <?php if (isset($product["track_listing"])) {
                if (strpos($product["track_listing"], ", ")) {
                  $track_listing_link = explode(", ", $product["track_listing"]);
                  echo "<div>";
                  foreach ($track_listing_link as $tlLink) {
                    echo "<p>" . $tlLink . "</p>";
                  }
                  echo "</div>";
                } else {
                  $position = 24;
                  $track_listing_link = $product["track_listing"];
                  echo "<a href='$track_listing_link' target='_blank'>" . substr_replace($track_listing_link, "<br>", $position, 0) . "</a>";
                }
              }
              ?>

            </div>
            <div class="productDetails__line">
              <h2>
                Number of discs:
              </h2>
              <p>
                <?php echo $product["number_of_discs/records"] ?>
              </p>
            </div>
            <div class="productDetails__line">
              <h2>
                Record Label:
              </h2>
              <p>
                <?php if (isset($product["Record Label"])) {
                  echo $product["Record Label"];
                } ?>
              </p>
            </div>
            <div class="productDetails__line">
              <h2>
                Pressing Country:
              </h2>
              <p>
                <?php if (isset($product["Pressing Country"])) {
                  echo $product["Pressing Country"];
                } ?>
              </p>
            </div>
            <div class="productDetails__line">

              <?php if (isset($product["Pressing Year"])) {
                echo "<h2>Pressing Year:</h2>";
                echo "<p>" . $product['Pressing Year'] . "</p>";
              } ?>
              </p>
            </div>
          </div>
          <?php if ($pro_stock > 0) {
            echo "<div class='productDetails__cart desktop'>
              <button class='productDetails__cartButton' type='button' id='" . $productID . "' name='add_cart'>
                <i class='fa fa-shopping-cart fa-xl'></i> Add to Cart
              </button>
            </div>";
          } else {
            echo "<div class='productDetails__cart desktop'>
              <button class='productDetails__cartButton' type='button' >
                </i> Request Stock
              </button>
            </div>";
          } ?>
        </div>
      </div>
    </div>
    <?php
    if (isset($video)) {
      echo "<div class='productDetails__bottom'>
              <iframe width='560' height='315' src='$convertedUrl' title='YouTube video player' frameborder='0'
                allow='accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share'
                allowfullscreen></iframe>
            </div>";
    } else {
      echo "<br><br>";
    }
    ?>
    </div>
  </main>
  <?php
  include('includes/footer.php');
  ?>
</body>

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
        document.getElementById("mySidenav").style.width = "250px";
        // Make the menu visible by adjusting the z-index
        $('.sidenav').css('z-index', '9999');

        // Add a dimming overlay to the body
        $('<div id="overlay"></div>').appendTo('body');
      }
      else if ($(window).width() <= 1200 && $(window).width() > 560) {
        document.getElementById("mySidenav").style.width = "50%";
      }
      else {
        document.getElementById("mySidenav").style.width = "100%";
      }
    });
    // Clicking on the cart icon in the menu.
    $('.menu__right').click(function () {
      window.location.href = 'cart.php';
      return false;
    });

    $('#reveal1').hide();
    $('#reveal2').hide();
    $('#Basic').show();
    $('#Details').hide();
    $('.tablinks[data-tab="Basic"]').addClass('active');

    $('.tablinks').on('click', function () {
      var tabName = $(this).data('tab');

      // Hide all tab content
      $('.productDetails__tabcontent').hide();

      // Remove active class from all tab buttons
      $('.tablinks').removeClass('active');

      // Show the selected tab content and mark the button as active
      $('#' + tabName).show();
      $(this).addClass('active');
    });

    $('#hint1').hover(function () {
      $('#reveal1').show()
    }, function () {
      $('#reveal1').hide()
    });
    $('#hint2').hover(function () {
      $('#reveal2').show()
    }, function () {
      $('#reveal2').hide()
    });

    var quantity = 1;

    //Reusable add to cart function.
    function AddToCart(e, button) {
      e.preventDefault();
      console.log(quantity)
      // Function for the adding to cart effect
      var cart = $('.menu__cart');
      var imgtodrag = $(button).parents('.productDetails__container').find("img").eq(0);
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
      var id = button.attr('id'); // Get the ID attribute of the button
      var data = {
        id: id,
        qty: quantity
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
    }

    $('select').on('change', function (e) {
      // Note the following syntax for finding and getting the data - it was the only way that worked!
      quantity = $(this).val();
      console.log("In first function: " + quantity)
    });
    // Clicking on the Add To Cart button
    // Binding it to '#myForm' allows both functions to work.
    $('.productDetails__cart').on('click', '.productDetails__cartButton', function (e) {
      var button = $(e.target); // Get the clicked button element
      if ($(this).attr('id')) {
        AddToCart(e, button);
      }
      else {
        window.location.href = "contact_us.php";
      }
    });

    $('.productDetails__left').on('click', '.productDetails__cartButton', function (e) {
      var button = $(e.target); // Get the clicked button element
      if ($(this).attr('id')) {
        AddToCart(e, button);
      }
      else {
        window.location.href = "contact_us.php";
      }
    });

    $(window).scroll(function () {
      var screenWidth = $(window).width();

      // Check if the screen is mobile-sized (less than or equal to 776px)
      if (screenWidth <= 776) {
        var scrollPosition = window.scrollY || window.pageYOffset;
        var windowHeight = window.innerHeight;
        var documentHeight = document.documentElement.scrollHeight;

        // Calculate the distance from the bottom of the page
        var distanceFromBottom = documentHeight - (scrollPosition + windowHeight);

        // Define the threshold (e.g., 100 pixels from the bottom)
        var threshold = 200;

        // Check if the distance from the bottom is less than the threshold
        if (distanceFromBottom < threshold) {
          // Hide the element
          $('.mobile').hide();
        } else {
          // Show the element
          $('.mobile').show();
        }
      }
    });


  });
</script>

</html>