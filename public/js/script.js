/* Load More */

$('.some-element').simpleLoadMore({
    item: '.element-item',
    count: 3,
    // itemsToLoad: 10,
    btnHTML: '<a href="#" class="load-more__btn">View More <i class="fas fa-angle-down"></i></a>'
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

