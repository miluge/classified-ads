/* Load More */

$('.some-element').simpleLoadMore({
    item: '.element-item',
    count: 3,
    // itemsToLoad: 10,
    btnHTML: '<div class="flex items-center justify-center"><button id="scroll" class="bg-blue-500 ml-4 hover:bg-blue-400 text-white font-bold py-2 px-4 border-b-4 border-blue-700 hover:border-blue-500 rounded overflow-visible"><i class="fas fa-arrow-up"></i></button><a href="#" class="load-more__btn bg-blue-500 ml-4 hover:bg-blue-400 text-white font-bold py-2 px-4 border-b-4 border-blue-700 hover:border-blue-500 rounded overflow-visible">View More <i class="fas fa-angle-down"></i></a></div>'
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

