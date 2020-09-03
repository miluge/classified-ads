/* Load More */

$('.some-element').simpleLoadMore({
    item: '.element-item',
    count: 6,
    // itemsToLoad: 10,
    btnHTML: '<div class="mt-4 "><a href="#" class="load-more__btn bg-blue-500 ml-4 hover:bg-blue-400 text-white font-bold py-2 px-6 border-b-4 border-blue-700 hover:border-blue-500 rounded overflow-visible">View More <i class="fas fa-angle-down"></i></a></div>'
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

