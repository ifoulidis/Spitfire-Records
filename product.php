<?php
include("includes/header.php");
include("includes/db.php");

$productID = $_GET["pro_id"];
$db = mysqli_connect("localhost", "root", "", "spitfire records");

$searchQuery = "select * from products WHERE id = $productID";
$searchResults = mysqli_query($db, $searchQuery);
$product = mysqli_fetch_array($searchResults);

// ###
// Get all the relevant properties of the product into usable variables.
$album = $product["album"];
$artist = $product["artist"];
if (isset($product["year"])) {
  $year = $product["year"];
} else {
  $year = "None";
}
$price = $product["regular_price"];
// The image from the database needs to be converted.
$encodedImage = base64_encode($product['front_image']);
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

$description = $product["description"];

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

if (isset($product["video_link"])) {
  $video = $product["video_link"];
  $convertedUrl = convertToEmbedUrl($video);
}


$pro_stock = $product['stock'];

// Set array or variable for genres
if (isset($product["genre1"])) {
  $genres = $product["genre1"];
}
if (isset($product["genre2"])) {
  $genres = $genres . ", " . $product["genre2"];
}
if (isset($product["genre3"])) {
  $genres = $genres . ", " . $product["genre3"];
}
/// ###
?>

<!-- Product details HTML -->

<div class="productDetails__container">
  <div class="productDetails__top">
    <div class="productDetails__left">
      <img class="productDetails__image" src="data:image/jpeg;base64,<?php echo $encodedImage; ?>" alt="Album Cover">
      <div class="productDetails__cart">
        <form id="myForm" method="post">
          <label for="quantitySelector">Quantity</label>
          <select name="quantitySelector" id="quantitySelector">
            <?php for ($i = 1; $i <= $pro_stock; $i++) {
              if ($i == $_SESSION['pro_qty']) {
                echo "<option selected value='$i' data-product_id='$productID' class='quantity form-control'>$i</option>";
              } else {
                echo "<option value='$i' data-product_id='$productID' class='quantity form-control'>$i</option>";
              }
            } ?>
          </select>

          <button class="cart" type="button" id="<?php echo $productID ?>" name="add_cart">
            <i class="fa fa-shopping-cart fa-xl"></i> Add to Cart
          </button>
        </form>
      </div>
    </div>
    <div class="productDetails__right">
      <div class="tab">
        <button class="tablinks" data-tab="Basic">Basic</button>
        <button class="tablinks" data-tab="Details">Details</button>
      </div>
      <div class="productDetails__tabcontent" id="Basic">

        <div class="productDetails__line">
          <h2>Album:</h2>
          <p class="productDetails__albumTitle">
            <?php echo $album; ?>
          </p>
        </div>
        <div class="productDetails__line">
          <h2>Artist:</h2>
          <p class="productDetails__artistTitle">
            <?php echo $artist; ?>
          </p>
        </div>
        <div class="productDetails__line">
          <h2>Year:</h2>
          <p>
            <?php echo $year; ?>
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
        <div class="productDetails__line">
          <h3>Track Listing:</h3>

          <?php if (isset($product["track_listing"])) {
            if (strpos($product["track_listing"], ", ")) {
              $track_listing_link = explode(", ", $product["track_listing"]);
              echo "<div>";
              foreach ($track_listing_link as $tlLink) {
                echo "<p>" . $tlLink . "</p>";
              }
              echo "</div>";
            } else {
              $track_listing_link = $product["track_listing"];
              echo "<a href='$track_listing_link'>" . $track_listing_link . "</a>";
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
    </div>


  </div>
</div>
<?php
if (isset($product["video_link"])) {
  echo "<div class='productDetails__bottom'>
              <hr>
              <h3>Video</h3>
              <p class='productDetails__description'></p>
              <iframe width='560' height='315' src='$convertedUrl' title='YouTube video player' frameborder='0'
                allow='accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share'
                allowfullscreen></iframe>
            </div>";
}
?>
</div>
<?php
include('includes/footer.php');
?>
</body>
<!-- JQuery for adding to cart -->
<script>
  $(document).ready(function () {

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
    $('select').on('change', function () {
      // Note the following syntax for finding and getting the data - it was the only way that worked!
      quantity = $(this).val();
      console.log("In first function: " + quantity)
    });
    // Clicking on the Add To Cart button
    // Binding it to '#myForm' allows both functions to work.
    $('#myForm').on('click', '.cart', function (e) {
      e.preventDefault(); // Prevent the default form submission
      console.log(quantity)
      var id = $(this).attr('id'); // Get the ID attribute of the button
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
    });

    // Clicking on the cart icon in the menu.
    $('.menu__cart').click(function () {
      window.location.href = 'cart.php';
      return false;
    });
  });
</script>

</html>