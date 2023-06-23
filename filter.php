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
    <!-- Filter Modal -->
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
            <a href="#" id="All" class="formatButton active" data-format="all">All</a>
            <a href="#" id="CDs" class="formatButton" data-format="CD">CDs</a>
            <a href="#" id="Vinyl" class="formatButton" data-format='12" Vinyl'>Vinyl</a>
          </div>
        </div>
        <div class="modal-section">
          <button id="applyFilters">Apply Filters</button>
        </div>
      </div>
    </div>

    <div class="searchLine">
      <form id="searchForm" class="searchbar" method="POST">
        <input type="search" id="searchbox" placeholder="Search for artist or album..." name="searchQuery">
        <button class="search" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
      </form>
      <a href="#" class="filter-button active">Filter</a>

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

<?php include("includes/footer.php"); ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function () {
    var offset = 0;
    function getParameterByName(name, url) {
      if (!url) url = window.location.href;
      name = name.replace(/[\[\]]/g, '\\$&');
      var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
        results = regex.exec(url);
      if (!results) return null;
      if (!results[2]) return '';
      history.pushState(null, "", location.href.split("?")[0]);
      return decodeURIComponent(results[2].replace(/\+/g, ' '));
    }
    var format = getParameterByName('format') || 'all';
    var searchQuery = "";
    var genre = getParameterByName('genre') || 'all';
    var gridItemsCount = $(".grid-container .product").length;

    $("#searchbox").on("input", function () {
      searchQuery = $(this).val(); // Update the variable with the input value
    });


    function updatePageButtons() {
      console.log(gridItemsCount);
      if (gridItemsCount === 15) {
        $('#nextPage').removeClass('disabled');
      }
      else {
        $('#nextPage').addClass('disabled');
      }
      if (offset === 0) {
        $('#prevPage').addClass('disabled');
      }
      else {
        $('#prevPage').removeClass('disabled');
      }
    }

    function getProducts() {
      data = {
        'action': 'getProducts',
        'offset_increment': offset,
        'search_query': searchQuery,
        'genre_option': genre,
        'format_option': format
      };
      $.post('includes/functions.php', data, function (response) {
        if (response.trim() === '') {
          if (gridItemsCount === 0) {
            $(".grid-container").html("<p>No results found.</p>");
            updatePageButtons();
          } else {
            console.log("No more results");
            $('#nextPage').addClass('disabled');
          }
        } else {
          $(".grid-container").html(response);
          gridItemsCount = $(".grid-container .product").length;
          updatePageButtons();
        }
      });
    }

    // Retrieve products
    getProducts();

    $("#nextPage").click(function (e) {
      e.preventDefault();
      if (gridItemsCount > 0) {
        offset += 15;
        getProducts();
      }
    });

    $("#prevPage").click(function (e) {
      e.preventDefault();
      if (offset !== 0) {
        offset -= 15;
        getProducts();
      }
      else {
        $('#prevPage').addClass('disabled');
      }
    });

    $("#searchForm input").on('search', function (e) {
      // Check if the Enter key was pressed (keyCode 13)
      e.preventDefault();
      getProducts();
    });

    $("#searchForm").submit(function (e) {
      e.preventDefault();
      getProducts();
    });

    $(".filter-button").click(function () {
      $("#filterModal").css("display", "block");
    });

    $(window).click(function (e) {
      if (e.target == document.getElementById("filterModal")) {
        $("#filterModal").css("display", "none");
      }
    });

    $("#applyFilters").click(function () {
      $("#filterModal").css("display", "none");
      getProducts();
    });

    // Function to activate genre button
    function activateGenreButton(button) {
      $('.genreButton').removeClass('active');
      button.addClass('active');
    }

    // Function to activate format button
    function activateFormatButton(button) {
      $('.formatButton').removeClass('active');
      button.addClass('active');
    }

    $('.formatButton').mousedown(function (e) {
      format = $(this).data('format');
      console.log(format);
      activateFormatButton($(this)); // Activate the clicked button
    });

    $('.modal-section .genreButton').click(function () {
      genre = $(this).data('genre');
      activateGenreButton($(this)); // Activate the clicked button
      console.log(genre);
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