<?php
session_start();
include("includes/header.php");
include("includes/db.php");
$searchQuery = "SELECT DISTINCT genre1 FROM products WHERE stock > 0
UNION SELECT DISTINCT genre2 FROM products WHERE stock > 0
UNION SELECT DISTINCT genre3 FROM products WHERE stock > 0";
global $db;
$searchResults = mysqli_query($db, $searchQuery);
$list = [];
?>

<main>
  <div class="home__container">
    <!-- Add the modal HTML -->
    <div id="filterModal" class="modal">
      <div class="modal-content">
        <h2>Filter Options</h2>
        <div class="modal-section">
          <h3>Genres</h3>
          <div class="genreContainer">
            <a href="#" class="genreButton active" data-genre="all">All</a>
            <?php
            while ($sortedGenre = mysqli_fetch_array($searchResults)) {
              $genreValue = $sortedGenre[0];
              if ($genreValue !== 'null') {
                $list[] = $genreValue;
              }
            }
            sort($list);
            foreach ($list as $item) {
              echo "<a href='#' class='genreButton' data-genre='$item'>$item</a>";
            }
            ?>
          </div>
        </div>
        <div class="modal-section">
          <h3>Formats</h3>
          <div class="formatContainer">
            <a href="#" class="formatButton active" data-format="all">All</a>
            <a href="#" class="formatButton" data-format="CD">CDs</a>
            <a href="#" class="formatButton" data-format='12" Vinyl'>Vinyl</a>
          </div>
        </div>
        <div class="modal-section">
          <button id="applyFilters">Apply Filters</button>
        </div>
      </div>
    </div>
    <div class="searchLine">
      <form id="searchForm" class="searchbar" method='POST'>
        <input type="search" id="searchbox" placeholder="Search for artist or album..." name="searchQuery">
        <button class='search' type='submit'><i class="fa-solid fa-magnifying-glass"></i></button>
      </form>
      <a href="#" class="filter-button">Filter</a>
    </div>

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
    // Initial varibles
    var initialData = { 'action': 'getProducts' };
    var offset = 0;
    var searchQuery = "";
    $("#searchbox").on("input", function () {
      searchQuery = $(this).val(); // Update the variable with the input value
    });


    function getProducts() {
      data = {
        'action': 'getProducts',
        'offset_increment': offset,
        'search_query': searchQuery
      };
      $.post('includes/functions.php', data, function (response) {
        $(".grid-container").html(response);
        console.log(response.error);
      });
    }


    // Retrieve products
    $.post('includes/functions.php', initialData, function (response) {
      $(".grid-container").html(response);
      console.log(response.error);
    });

    $("#nextPage").click(function (e) {
      offset += 15;
      getProducts();
    });

    $("#prevPage").click(function (e) {
      e.preventDefault();
      offset -= 15;
      getProducts();
    });

    $("#searchForm input").keypress(function (e) {
      if (e.which === 13) { // Check if the Enter key was pressed (keyCode 13) // Prevent the form from submitting normally
        getProducts();
      }
    });
    $("#searchForm").submit(function (e) {
      e.preventDefault();
      getProducts();
    })

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