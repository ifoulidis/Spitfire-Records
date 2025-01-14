<?php
// Add delete product functionality
// Add search functionality
// Add pagination
session_start();
if (!isset($_SESSION['admin_email'])) {
  echo "<script>window.open('log_in.php','_self')</script>";
} else {
  include("../includes/db.php");

  $genraQuery = "SELECT DISTINCT genre1 FROM products
    UNION SELECT DISTINCT genre2 FROM products
    UNION SELECT DISTINCT genre3 FROM products";
  global $con;
  $searchResults = mysqli_query($con, $genraQuery);
  $list = [];
  ?>

  <!DOCTYPE html>
  <html>

  <head>
    <title>Update Products</title>
    <link href="../styles/style-v3.css" rel="stylesheet">
    <link href="css/admin_style.css" rel="stylesheet">

    <link
      href="https://fonts.googleapis.com/css?family=Handlee|Roboto:wght@100,400|Courgette|Bruno+Ace|New+Rocker|Space+Grotesk:400,700|Montserrat:400,700|Roboto&display=swap"
      rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

  </head>

  <body>
    <a href="index.php" class="back-button"><i class="fa-solid fa-arrow-left"></i> Back</a>
    <div class="mainTitle">
      <h1>Update Products</h1>
    </div>
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
              if ($item !== 'null') {
                $url_key = rawurlencode($item);
                echo "<a href='#' class='genreButton' data-genre='$url_key'>$item</a>";
              }
            }
            ?>
          </div>
        </div>
        <div class="modal-section">
          <h3>Formats</h3>
          <div class="formatContainer">
            <a href="#" id="All" class="formatButton active" data-format="all">All</a>
            <a href="#" id="CDs" class="formatButton" data-format="CD">CDs</a>
            <a href="#" id="Vinyl LP" class="formatButton" data-format='Vinyl LP'>Vinyl LP</a>
            <a href="#" id="7 Inch Vinyl" class="formatButton" data-format='7 Inch Vinyl'>7&quot; Vinyl</a>
          </div>
        </div>
        <div class="modal-section">
          <h3>Condition</h3>
          <div class="conditionContainer">
            <label><input type="radio" name="condition" value="all" checked>All</label>
            <label><input type="radio" name="condition" value="new">New</label>
            <label><input type="radio" name="condition" value="used">Used</label>
          </div>
        </div>
        <div class="modal-section">
          <button id="applyFilters">Apply Filters</button>
          <button id="resetFilters">Reset Filters</button>
        </div>
      </div>
    </div>

    <div class="searchLine">
      <form id="searchForm" class="searchbar" method="POST">
        <input type="search" id="searchbox" placeholder="Search for artist or album..." name="searchQuery">
        <button class="search" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
      </form>
      <a href="#" class="filter_button active">Filter</a>
      <button id="clearFilters">Clear Filters</button>
    </div>
    <table>
      <div class="bars-1"></div>
    </table>


    <!-- Pagination links -->
    <div class="center">
      <div class="home__paginator">
        <button id="prevPage">❮</button>
        <button id="nextPage">❯</button>
      </div>
    </div>
  </body>

  <script>
    $(document).ready(function () {
      $('table').on('click', '.deleteProduct', function (e) {
        e.preventDefault();
        var button = $(this);
        var productId = $(this).data('productid');
        var status = $(this).contents();

        var delete_url = "functions/delete_product.php?id=" + productId;
        // AJAX request to update the fulfillment status           
        $.ajax({
          url: delete_url, type: 'GET', success: function (response) {  // Reload the page after successful update               
            $("table").load(location.href + " table");
          },
          error: function (xhr, status, error) {  // Handle error case              
            console.log(error);
          }
        });
      });

      $('table').on('click', '.updateLink', function (e) {
        e.preventDefault();
        var button = $(this);
        var productId = $(this).data('productid');
        var update_url = "https://spitfirerecords.co.nz/admin/update_product.php?id=" + productId + "&return=" + encodeURIComponent(window.location.href);
        window.location.replace(update_url);
      });


      function getParameterByName(name, url) {
        if (!url) url = window.location.href;
        name = name.replace(/[\[\]]/g, '\\$&');
        var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
          results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, ' '));
      }


      var format = getParameterByName('format') || 'all';
      var searchQuery = getParameterByName('genre') || "";
      var genre = getParameterByName('genre') || 'all';
      var condition = getParameterByName('condition') || "all";
      var offset = Number((getParameterByName('offset'))) || 0;
      var gridItemsCount = $(".grid-container .product").length;

      $("#searchbox").on("input", function () {
        searchQuery = $(this).val(); // Update the variable with the input value
      });


      function updatePageButtons() {
        console.log(gridItemsCount);
        if (gridItemsCount === 16) {
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

      function addQueryParamsToURL() {
        var url = window.location.href;

        // Parse the existing query parameters
        var urlParts = url.split('?');
        var baseUrl = urlParts[0];
        var queryParams = urlParts[1] ? urlParts[1].split('&') : [];

        // Update or remove existing query parameters
        queryParams = queryParams.filter(function (param) {
          return !param.startsWith('format=') && !param.startsWith('searchQuery=') && !param.startsWith('genre=') && !param.startsWith('condition=') && !param.startsWith('offset=');
        });

        // Add the new query parameters
        if (format) queryParams.push('format=' + encodeURIComponent(format));
        if (searchQuery) queryParams.push('searchQuery=' + encodeURIComponent(searchQuery));
        if (genre) queryParams.push('genre=' + encodeURIComponent(genre));
        if (condition) queryParams.push('condition=' + encodeURIComponent(condition));
        if (offset) queryParams.push('offset=' + encodeURIComponent(offset));

        // Combine the base URL and updated query parameters
        var newUrl = baseUrl;
        if (queryParams.length > 0) {
          newUrl += '?' + queryParams.join('&');
        }

        // Modify the URL using pushState
        history.pushState(null, '', newUrl);
      }


      function getProducts() {
        data = {
          'action': 'getProducts',
          'offset_increment': offset,
          'search_query': searchQuery,
          'genre_option': genre,
          'condition': condition,
          'format_option': format,
          'url': window.location.href
        };
        console.log(data);
        $('.bars-1').show();
        $.post('functions/get_products.php', data, function (response) {
          if (response.trim() === '') {
            if (offset > 0) {
              $('#nextPage').addClass('disabled');
              addQueryParamsToURL();
            } else {
              $("table").html("<p>No results found!</p>");
              updatePageButtons();
            }
          } else {
            $('.bars-1').hide();
            $("table").html(response);
            gridItemsCount = $("table tr").length;
            updatePageButtons();
            addQueryParamsToURL();
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
        offset = 0;
        getProducts();
      });

      $("#searchForm").submit(function (e) {
        e.preventDefault();
        offset = 0;
        getProducts();
      });

      $(".filter_button").click(function () {
        $("#filterModal").css("display", "block");
      });

      $(window).click(function (e) {
        if (e.target == document.getElementById("filterModal")) {
          $("#filterModal").css("display", "none");
        }
      });

      $("#applyFilters").click(function () {
        $("#filterModal").css("display", "none");
        offset = 0;
        getProducts();
      });

      function resetFilters() {
        // Reset Buttons
        $(".genreButton").removeClass("active");
        $(".genreButton[data-genre='all']").addClass("active");
        $('.formatButton').removeClass('active');
        $(".formatButton[data-format='all']").addClass("active");
        $("input[name='condition'][value='all']").prop("checked", true);
        // Reset variables
        genre = 'all';
        format = "all";
        condition = "all";
        searchQuery = '';
        $("#filterModal").css("display", "none");
        getProducts();
      }

      $("#resetFilters").click(function () {
        resetFilters();
      });

      $("#clearFilters").click(function (e) {
        resetFilters();
      });

      $("[name='condition']").click(function () {
        var cond = $("input[name='condition']:checked").val();
        if (cond == "new") {
          condition = 0;
        }
        else if (cond == "used") {
          condition = 1;
        }
        else {
          condition = cond;
        }
      })

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

    });
  </script>



  </html>

<?php } ?>