;(function ($) {
"use strict";


        /*--
        Menu Sticky
    -----------------------------------*/
    var $window = $(window);
    
    $window.on('scroll', function() {
        var scroll = $window.scrollTop();
        if (scroll < 300) {
            $(".header-navbar").removeClass("stick");
        }else{
            $(".header-navbar").addClass("stick");
        }
    });
    

    

    
    $('.testimonial-slider').owlCarousel({
        center: true,
        items: 2,
        loop: true,
        margin: 30,
        responsive: {
          600: {
            items: 4
          }
        }
    });
    
    $(".collapse.show").each(function(){
        $(this).prev(".card-header").find(".fa").addClass("fa-minus").removeClass("fa-plus");
    });

    // Toggle plus minus icon on show hide of collapse element
    $(".collapse").on('show.bs.collapse', function(){
        $(this).prev(".card-header").find(".fa").removeClass("fa-plus").addClass("fa-minus");
    }).on('hide.bs.collapse', function(){
        $(this).prev(".card-header").find(".fa").removeClass("fa-minus").addClass("fa-plus");
    });


    




})(jQuery);