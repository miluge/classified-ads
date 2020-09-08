/* Load More */

$('.some-element').simpleLoadMore({
    item: '.element-item',
    count: 6,
    // itemsToLoad: 10,
    btnHTML: '<div id="view" class="flex mt-4 w-full justify-center"><a href="#" class="load-more__btn bg-blue-500 ml-4 hover:bg-blue-400 text-white font-bold py-2 px-6 border-b-4 border-blue-700 hover:border-blue-500 rounded overflow-visible">View More <i class="fas fa-angle-down"></i></a></div>'
  });

/* Smooth Scroll to top */

$(document).ready(function(){ 
    $(window).scroll(function(){ 
        if ($(this).scrollTop() > 100) { 
            $('#scroll').fadeIn(); 
        } else { 
            $('#scroll').fadeOut(); 
        } 
    }); 
    $('#scroll').click(function(){ 
        $("html, body").animate({ scrollTop: 0 }, 600); 
        return false; 
    }); 
});


/* Hide / Show */

function showCat(catType) {
    $('div[data-category-name="Real estate"]').toggle(
        catType == "all" || catType == "Real estate"
    );
    $('div[data-category-name="Auto"]').toggle(
        catType == "all" || catType == "Auto"
    );
    $('div[data-category-name="Work"]').toggle(
        catType == "all" || catType == "Work"
    );
    $('div[data-category-name="Animals"]').toggle(
        catType == "all" || catType == "Animals"
    );
    $('div[data-category-name="Services"]').toggle(
        catType == "all" || catType == "Services"
    );
    $('div[data-category-name="Holiday"]').toggle(
        catType == "all" || catType == "Holiday"
    );
    $('div[data-category-name="Business"]').toggle(
        catType == "all" || catType == "Business"
    );
    $('div[data-category-name="Other"]').toggle(
        catType == "all" || catType == "Other"
    );
  }

// Search 

function mySearch() {
    var input, filter, a, item, i, txtValue;
    input = document.getElementById("mySearch");
    filter = input.value.toUpperCase();
    a = document.getElementsByClassName("articlelink");  for (i = 0; i < a.length; i++) {
      item = a[i].getElementsByClassName('title')[0];
      txtValue = item.textContent || item.innerText;
      console.log(txtValue);
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        a[i].parentElement.style.display = "";
      } else {
        a[i].parentElement.style.display = "none";
      }
    }
  }