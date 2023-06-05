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
$year = $product["year"];
$price = $product["regular_price"];
// The image from the database needs to be converted.
$encodedImage = base64_encode($product['front_image']);
if ($product["on_sale"] == 1) {
  $sale_price = $product["sale_price"];
}

if ($product["new/used"] == 1) {
  $media_condition = "Brand New";
  $insert_condition = "Brand New";
} elseif ($product["new/used"] == 0) {
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
<main>
  <div class="productDetails__container">
    <div class="productDetails__top">
      <div class="productDetails__left">
        <img class="productDetails__image" src="data:image/jpeg;base64,<?php echo $encodedImage; ?>" alt="Album Cover">

      </div>
      <div class="productDetails__right">
        <div class="tab">
          <button class="tablinks" data-tab="Basic">Basic</button>
          <button class="tablinks" data-tab="Details">Details</button>
        </div>
        <div class="productDetails__tabcontent" id="Basic">
          <table>
            <tr>
              <td>Album:</td>
              <td>
                <p class="productDetails__albumTitle">
                  <?php echo $album; ?>
                </p>
              </td>
            </tr>
            <tr>
              <td>Artist:</td>
              <td>
                <p class="productDetails__artistTitle">
                  <?php echo $artist; ?>
                </p>
              </td>
            </tr>
            <tr>
              <td>Year:</td>
              <td>
                <p>
                  <?php echo $year; ?>
                </p>
              </td>
            </tr>
            <tr>
              <td>Genres:</td>
              <td>
                <p class="productDetails__genres">
                  <?php echo $genres; ?>
                </p>
              </td>
            </tr>
            <tr>
              <td rowspan="3">Description:</td>
              <td rowspan="3">
                <p class="productDetails__description">
                  <?php echo $description; ?>
                </p>
              </td>
            </tr>
          </table>
        </div>

        <div class="productDetails__tabcontent" id="Details">
          <table>
            <tr>
              <td>
                <h3>Track Listing:
              </td>
              <td>
                <?php if (isset($product["track_listing"])) {
                  if (strpos($product["track_listing"], ", ")) {
                    $track_listing_link = explode(", ", $product["track_listing"]);
                    echo $track_listing_link;
                  } else {
                    $track_listing_link = $product["track_listing"];
                    echo "<a href='$track_listing_link'>" . $track_listing_link . "</a>";
                  }

                }
                ?>
              </td>
            </tr>
            <tr>
              <td>
                Number of discs:
              </td>
              <td>
                <p>
                  <?php echo $product["number_of_discs/records"] ?>
                </p>
              </td>
            </tr>
            <tr>
              <td>
                Record Label:
              </td>
              <td>
                <p>
                  <?php if (isset($product["Record Label"])) {
                    echo $product["Record Label"];
                  } ?>
                </p>
              </td>
            </tr>
            <tr>
              <td>
                Pressing Country:
              </td>
              <td>
                <p>
                  <?php if (isset($product["Pressing Country"])) {
                    echo $product["Pressing Country"];
                  } ?>
                </p>
              </td>
            </tr>
            <tr>
              <td>
                Pressing Year:
              </td>
              <td>
                <p>
                  <?php if (isset($product["Pressing Year"])) {
                    echo $product["Pressing Year"];
                  } ?>
                </p>
              </td>
            </tr>
          </table>

        </div>

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
</main>
<?php
include('includes/footer.php');
?>
</body>
<!-- JQuery for adding to cart -->
<script>
  $(document).ready(function () {

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