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
  <div class="home__container">
    <!-- Filter Modal -->
    <div id="filterModal" class="modal">
      <div class="modal-content">
        <h2>Filter Options</h2>

        <div class="modal-section">
          <h3>Genres</h3>
          <div class="genreContainer">
            <button class="genreButton active" data-genre="all">All</button>
            <?php
            while ($sortedGenre = mysqli_fetch_array($searchResults)) {
              $genreValue = $sortedGenre[0];
              if ($genreValue !== 'null' and $genreValue !== '') {
                $list[] = $genreValue;
              }
            }
            sort($list);
            foreach ($list as $item) {
              echo "<button class='genreButton' data-genre='$item'>$item</button>";
            }
            ?>
          </div>

        </div>
        <div class="modal-section">
          <h3>Formats</h3>
          <div class="formatContainer">
            <button id="All" class="formatButton active" data-format="all">All</button>
            <button id="CDs" class="formatButton" data-format="CD">CDs</button>
            <button id="Vinyl" class="formatButton" data-format='Vinyl LP'>Vinyl</button>
            <button id="DVDs" class="formatButton" data-format='Music DVD'>Music DVDs</button>
            <button id='7" vinyl' class="formatButton" data-format='7 Inch Vinyl'>7&quot; vinyl</button>
            <button id='Cassette' class="formatButton" data-format='Cassette'>Cassettes</button>
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
          <div class="applyFiltersContainer">
            <button id="applyFilters">Apply Filters</button>
            <button id="resetFilters">Reset Filters</button>
          </div>
        </div>
      </div>
    </div>

    <div class="searchLine">
      <form id="searchForm" class="searchbar" method="POST">
        <input type="search" id="searchbox" placeholder="Search for artist or album..." name="searchQuery">
        <button class="search" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
      </form>
      <div class="filter_div">
        <a href="#" class="filter-button active">Filter</a>
        <select id="sortDropdown">
          <option value="default" selected>Relevance</option>
          <option value="price_high_low">Price (Highest to Lowest)</option>
          <option value="price_low_high">Price (Lowest to Highest)</option>
          <option value="album_a_z">Album (A to Z)</option>
          <option value="album_z_a">Album (Z to A)</option>
          <option value="artist_a_z">Artist (A to Z)</option>
          <option value="artist_z_a">Artist (Z to A)</option>
        </select>
      </div>
    </div>

    <div class="grid-container">
      <!-- Loading bars -->
      <div class="bars-1"></div>
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
</body>

<script>
  $(document).ready(function () {

    // Get filters from URL and activate the appropriate buttons in the modal.
    var format = getParameterByName('format') || 'all';
    activateFormatButton($('.formatButton[data-format="' + format + '"]'));
    var searchQuery = getParameterByName('searchQuery') || "";
    var genre = getParameterByName('genre') || 'all';
    activateGenreButton($('.genreButton[data-genre="' + genre + '"]'));
    var condition = getParameterByName('condition') || "all";
    activateConditionButton(condition);
    var order = getParameterByName('order') || "default";
    var offset = Number(getParameterByName('offset')) || 0;
    var gridItemsCount = $(".grid-container .product").length;


    $("#searchbox").on("input", function () {
      searchQuery = $(this).val(); // Update the variable with the input value
    });

    function getParameterByName(name, url) {
      if (!url) url = window.location.href;
      name = name.replace(/[\[\]]/g, '\\$&');
      var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
        results = regex.exec(url);
      if (!results) return null;
      if (!results[2]) return '';

      // Remove the hash portion from the parameter value if present
      var value = results[2].replace(/#.*$/, '');

      return decodeURIComponent(value.replace(/\+/g, ' '));
    }

    function addQueryParamsToURL() {
      var url = window.location.href;

      // Parse the existing query parameters
      var urlParts = url.split('?');
      var baseUrl = urlParts[0];
      var queryParams = urlParts[1] ? urlParts[1].split('&') : [];

      // Update or remove existing query parameters
      queryParams = queryParams.filter(function (param) {
        return !param.startsWith('format=') && !param.startsWith('searchQuery=') && !param.startsWith('genre=') && !param.startsWith('condition=') && !param.startsWith('order=') && !param.startsWith('offset=');
      });

      // Add the new query parameters if they are not their default values
      if (format && format !== 'all') queryParams.push('format=' + encodeURIComponent(format));
      if (searchQuery && searchQuery !== '') queryParams.push('searchQuery=' + encodeURIComponent(searchQuery));
      if (genre && genre !== 'all') queryParams.push('genre=' + encodeURIComponent(genre));
      if (condition && condition !== 'all') queryParams.push('condition=' + encodeURIComponent(condition));
      if (order && order !== 'default') queryParams.push('order=' + encodeURIComponent(order));
      if (offset && offset !== 0) queryParams.push('offset=' + encodeURIComponent(offset));

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
        'orderby': order,
        'format_option': format
      };
      console.log(data);
      $('.bars-1').show();
      $.post('includes/functions.php', data, function (response) {
        if (response) {
          $('html, body').animate({ scrollTop: '0px' }, 300);
          $('.bars-1').hide();
          $(".grid-container").html(response);
          gridItemsCount = $(".grid-container .product").length;
          if (gridItemsCount < 16) {
            $('#nextPage').hide();
          } else {
            $('#nextPage').show();
          }
          if (offset === 0) {
            $('#prevPage').hide();
          } else {
            $('#prevPage').show();
          }
          addQueryParamsToURL();
        } else {
          if (offset > 0) {
            $('#nextPage').hide();
            addQueryParamsToURL();
          } else {
            $('#nextPage').hide();
            $('#prevPage').hide();
            $(".grid-container").html("<p style='text-align:center;'>No results found!</p>");
            addQueryParamsToURL();
          }
        }
      });
    }

    // Retrieve products
    getProducts();

    $("#nextPage").click(function (e) {
      e.preventDefault();
      if (gridItemsCount === 16) {
        offset += 16;
        getProducts();
      }
    });

    $("#prevPage").click(function (e) {
      e.preventDefault();
      if (offset !== 0) {
        offset -= 16;
        getProducts();
      }
      else {
        $('#prevPage').hide();
      }
    });

    // Sorting functionality
    $("#sortDropdown").change(function () {
      order = $(this).val();
      $("#sortDropdown select").val($(this).val());
      getProducts();
    });

    $("#searchForm input").on('keypress search', function (e) {
      // Check if the Enter key was pressed (keyCode 13) or the search input was cleared
      if (e.keyCode === 13 || e.type === 'search') {
        e.preventDefault();
        offset = 0;
        getProducts();
      }
    });


    $("#searchForm").submit(function (e) {
      e.preventDefault();
      offset = 0;
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
      offset = 0;
      getProducts();
    });

    function resetFilters() {
      $(".genreButton").removeClass("active");
      $(".genreButton[data-genre='all']").addClass("active");
      $('.formatButton').removeClass('active');
      $(".formatButton[data-format='all']").addClass("active");
      $("input[name='condition'][value='all']").prop("checked", true);
      // Reset variables
      genre = 'all';
      format = "all";
      condition = "all";
      getProducts();
    }

    $("#resetFilters").click(function () {
      $("#filterModal").css("display", "none");
      // Reset Buttons
      resetFilters()
    });

    function activateConditionButton(value) {
      $('input[name="condition"]').prop('checked', false);
      $('input[name="condition"][value="' + value + '"]').prop('checked', true);
    }

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


    // Clicking on the Add To Cart button
    // Binding it to .grid-container' allows both functions to work.
    $('.grid-container').on('click', '.cart', function (e) {
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