$( document ).on( "affPageReady", function( ) {

    $(".filter-and-sort-products").on('click', function() {
        $('input[name="'+$(this).data('sort-key')+'"]').val($(this).data('sort-value'));
        $('input[name="'+$(this).data('sort-key')+'"]').closest('form').submit();
    });


    $(".btn-clear-filter").on('click', function() {
        $(this).closest('form').find('input').val("");
        $(this).closest('form').submit();
    });

    
    $('.product_slider').owlCarousel({
        margin: 0,
        nav: true,
        loop: true,
        dots: false,
        autoplay: false,
        items: 1,
        ltr: false,
        smartSpeed: 450,

        responsive: {
            0: {
                items: 1
            },
            600: {
                items: 1,
                nav: false,
            },
            1000: {
                items: 1,
                nav: true,
            }
        }
    })

    $('#slider-home').owlCarousel({
        margin: 0,
        nav: false,
        ltr: false,
        loop: true,
        dots: false,
        autoplay: false,
        items:1,
        smartSpeed:450,
        responsive: {
            0: {
                items: 1
            },
            600: {
                items: 1
            },
            1000: {
                items: 1
            }
        }
    })

    $('#categorySlider').owlCarousel({
        margin: 30,
        nav: false,
        loop: true,
        dots: false,
        autoplay: true,
        items: 1,
        ltr: false,
        smartSpeed: 450,

        responsive: {
            0: {
                items: 1
            },
            600: {
                items: 4
            },
            1000: {
                items: 6
            }
        }
    })

    $('#pupularCat').owlCarousel({
        margin: 0,
        nav: true,
        loop: true,
        dots: false,
        autoplay: true,
        items: 1,
        ltr: false,
        smartSpeed: 450,

        responsive: {
            0: {
                items: 1
            },
            600: {
                items: 1,
                nav: false,
            },
            1000: {
                items: 1,
                nav: true,
            }
        }
    })

    $('#premusAds').owlCarousel({
        margin: 30,
        nav: true,
        loop: true,
        dots: false,
        autoplay: true,
        items: 1,
        ltr: false,
        smartSpeed: 450,

        responsive: {
            0: {
                items: 1
            },
            600: {
                items: 2,
                nav: false,
            },
            1000: {
                items: 3,
                nav: true,
            }
        }
    })

    $(".owl-prev").html('<i class="fa-solid fa-arrow-left-long"></i>');
    $(".owl-next").html('<i class="fa-solid fa-arrow-right-long"></i>');

    $('.search-location .dropdown-menu li').on('click', function() {
        $('.search-location .dropdown-select').html('<i class="fas fa-map-marker-alt"></i> '+$(this).text()+'<span class="caret"></span>');
    });

    $('.search-category .dropdown-menu li').on('click', function() {
        $('.search-category .dropdown-select').html('<i class="fas fa-map-marker-alt"></i> '+$(this).text()+'<span class="caret"></span>');
    });


    $('ul.menu li a').click(function() {
        var $this = $(this);
        $this.parent().siblings().removeClass('active').end().addClass('active');
    });
    
    $('.view-switcher ul li').on('click',function(e) {
        if ($(this).hasClass('listview')) {
            $('.listing-main').removeClass('gridview').addClass('listview');
        }
        else if($(this).hasClass('gridview')) {
            $('.listing-main').removeClass('listview').addClass('gridview');
        }
    });
    
    $('.view-switcher ul li').on('click',function(e) {
        if ($(this).hasClass('listview')) {
            $('.view-switcher ul li.gridview').removeClass('active');
            $('.view-switcher ul li.listview').addClass('active');
        } else if($(this).hasClass('gridview')) {
            $('.view-switcher ul li.listview').removeClass('active');
            $('.view-switcher ul li.gridview').addClass('active');
        }
    });


    $("[data-bg-image]").each(function () {
        var img = $(this).data("bg-image");
        $(this).css({
            backgroundImage: "url(" + img + ")"
        });
    });


    $("#preloader").fadeOut("slow", function () {
        $(this).remove();
    });

    $('[data-type="section-switch"]').on('click', function () {
        if (location.pathname.replace(/^\//, '') === this.pathname.replace(/^\//, '') && location.hostname === this.hostname) {
            var target = $(this.hash);
            if (target.length > 0) {

                target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
                $('html,body').animate({
                    scrollTop: target.offset().top
                }, 1000);
                return false;
            }
        }
    });

    $(window).on('scroll', function () {

        // Back Top Button
        if ($(window).scrollTop() > 500) {
            $('.scrollup').addClass('back-top');
        } else {
            $('.scrollup').removeClass('back-top');
        }
        // Sticky Header
        if ($('body').hasClass('sticky-header')) {
            var stickyPlaceHolder = $("#rt-sticky-placeholder"),
                menu = $("#header-menu"),
                menuH = menu.outerHeight(),
                topHeaderH = $('#header-topbar').outerHeight() || 0,
                middleHeaderH = $('#header-middlebar').outerHeight() || 0,
                targrtScroll = topHeaderH + middleHeaderH;
            if ($(window).scrollTop() > targrtScroll) {
                menu.addClass('rt-sticky');
                stickyPlaceHolder.height(menuH);
            } else {
                menu.removeClass('rt-sticky');
                stickyPlaceHolder.height(0);
            }
        }
    });

    if ($.fn.meanmenu) {
        $('nav#dropdownmain').meanmenu({
            siteLogo: $('#meanmenu-content').html()
        });
    }

    var counterContainer = $('.counter');
    if (counterContainer.length) {
        counterContainer.counterUp({
            delay: 50,
            time: 2000
        });
    }


    $('.product-view-trigger').on('click', function (e) {
        var self = $(this),
            data = self.attr("data-type"),
            target = $("#product-view");

        localStorage.setItem("catalog_product_view", data);

        self.parents('.layout-switcher').find('li.active').removeClass('active');
        self.parent('li').addClass('active');
        target.children('.row').find('>div').animate({
            opacity: 0,
        }, 20, function () {
            if (data === "product-box-grid") {
                target.removeClass('product-box-list');
                target.addClass('product-box-grid');
            } else if (data === "product-box-list") {
                target.removeClass('product-box-grid');
                target.addClass('product-box-list');
            }

            target.find('.d-none').removeClass('d-none');

            target.children('.row').find('>div').animate({
                opacity: 1,
            }, 10);
        });
        e.preventDefault();
        return false;
    });


    let catalog_product_view = localStorage.getItem("catalog_product_view");

    if(catalog_product_view != null) {
        $('[data-type="'+catalog_product_view+'"]').trigger('click');
    } else {
        $('[data-type="product-box-grid"]').trigger('click');
    }

    $('.classima-phone-reveal').on('click', function() {
        if ($(this).hasClass('not-revealed')) {
            $(this).removeClass('not-revealed').addClass('revealed');
            var phone = $(this).data('phone');
            $(this).find('span').text(phone);
        }
        return false;
    });

    $('.animated-headline').animatedHeadline({
        animationType: 'type',
        revealDuration: 500,
    });

    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        elevateZoom();
    });

    function elevateZoom() {
        if ($.fn.elevateZoom !== undefined) {
            $('.zoom_01').elevateZoom({
                zoomType : "lens",
                lensShape : "round",
                lensSize : 200
            });
        }
    }

    elevateZoom();

    $('[data-toggle="tooltip"]').tooltip()

    var $container = $(".isotope-wrap");
    if ($container.length > 0) {
        var $isotope;
        var blogGallerIso = $(".featuredContainer", $container).imagesLoaded(function () {
            $isotope = $(".featuredContainer", $container).isotope({
                filter: "*",
                transitionDuration: "1s",
                hiddenStyle: {
                    opacity: 0,
                    transform: "scale(0.001)"
                },
                visibleStyle: {
                    transform: "scale(1)",
                    opacity: 1
                }
            });
        });
        $container.find(".isotope-classes-tab").on("click", "a", function () {
            var $this = $(this);
            $this
                .parent(".isotope-classes-tab")
                .find("a")
                .removeClass("current");
            $this.addClass("current");
            var selector = $this.attr("data-filter");
            $isotope.isotope({
                filter: selector
            });
            return false;
        });
    }

    if ($("#googleMap").length) {
        window.onload = function () {
            var styles = [{
                featureType: 'water',
                elementType: 'geometry.fill',
                stylers: [{
                    color: '#b7d0ea'
                }]
            }, {
                featureType: 'road',
                elementType: 'labels.text.fill',
                stylers: [{
                    visibility: 'off'
                }]
            }, {
                featureType: 'road',
                elementType: 'geometry.stroke',
                stylers: [{
                    visibility: 'off'
                }]
            }, {
                featureType: 'road.highway',
                elementType: 'geometry',
                stylers: [{
                    color: '#c2c2aa'
                }]
            }, {
                featureType: 'poi.park',
                elementType: 'geometry',
                stylers: [{
                    color: '#b6d1b0'
                }]
            }, {
                featureType: 'poi.park',
                elementType: 'labels.text.fill',
                stylers: [{
                    color: '#6b9a76'
                }]
            }];
            var options = {
                mapTypeControlOptions: {
                    mapTypeIds: ['Styled']
                },
                center: new google.maps.LatLng(-37.81618, 144.95692),
                zoom: 10,
                disableDefaultUI: true,
                mapTypeId: 'Styled'
            };
            var div = document.getElementById('googleMap');
            var map = new google.maps.Map(div, options);
            var styledMapType = new google.maps.StyledMapType(styles, {
                name: 'Styled'
            });
            map.mapTypes.set('Styled', styledMapType);

            var marker = new google.maps.Marker({
                position: map.getCenter(),
                animation: google.maps.Animation.BOUNCE,
                icon: 'media/map-marker.png',
                map: map
            });
        };
    }

    $(function () {
        $(".rc-carousel").each(function () {
            var carousel = $(this),
                loop = carousel.data("loop"),
                Canimate = carousel.data("animate"),
                items = carousel.data("items"),
                margin = carousel.data("margin"),
                stagePadding = carousel.data("stage-padding"),
                autoplay = carousel.data("autoplay"),
                autoplayTimeout = carousel.data("autoplay-timeout"),
                smartSpeed = carousel.data("smart-speed"),
                dots = carousel.data("dots"),
                nav = carousel.data("nav"),
                navSpeed = carousel.data("nav-speed"),
                rXsmall = carousel.data("r-x-small"),
                rXsmallNav = carousel.data("r-x-small-nav"),
                rXsmallDots = carousel.data("r-x-small-dots"),
                rXmedium = carousel.data("r-x-medium"),
                rXmediumNav = carousel.data("r-x-medium-nav"),
                rXmediumDots = carousel.data("r-x-medium-dots"),
                rSmall = carousel.data("r-small"),
                rSmallNav = carousel.data("r-small-nav"),
                rSmallDots = carousel.data("r-small-dots"),
                rMedium = carousel.data("r-medium"),
                rMediumNav = carousel.data("r-medium-nav"),
                rMediumDots = carousel.data("r-medium-dots"),
                rLarge = carousel.data("r-large"),
                rLargeNav = carousel.data("r-large-nav"),
                rLargeDots = carousel.data("r-large-dots"),
                rExtraLarge = carousel.data("r-extra-large"),
                rExtraLargeNav = carousel.data("r-extra-large-nav"),
                rExtraLargeDots = carousel.data("r-extra-large-dots"),
                center = carousel.data("center"),
                custom_nav = carousel.data("custom-nav") || "";
            carousel.addClass('owl-carousel');
            var owl = carousel.owlCarousel({
                loop: loop ? true : false,
                animateOut: Canimate,
                items: items ? items : 1,
                lazyLoad: true,
                margin: margin ? margin : 0,
                autoplay: autoplay ? true : false,
                autoplayTimeout: autoplayTimeout ? autoplayTimeout : 1000,
                smartSpeed: smartSpeed ? smartSpeed : 250,
                dots: dots ? true : false,
                nav: nav ? true : false,
                navText: [
                    '<i class="fa fa-angle-left" aria-hidden="true"></i>',
                    '<i class="fa fa-angle-right" aria-hidden="true"></i>'
                ],
                navSpeed: navSpeed ? true : false,
                center: center ? true : false,
                responsiveClass: true,
                responsive: {
                    0: {
                        items: rXsmall ? rXsmall : 1,
                        nav: rXsmallNav ? true : false,
                        dots: rXsmallDots ? true : false
                    },
                    576: {
                        items: rXmedium ? rXmedium : 2,
                        nav: rXmediumNav ? true : false,
                        dots: rXmediumDots ? true : false
                    },
                    768: {
                        items: rSmall ? rSmall : 3,
                        nav: rSmallNav ? true : false,
                        dots: rSmallDots ? true : false
                    },
                    992: {
                        items: rMedium ? rMedium : 4,
                        nav: rMediumNav ? true : false,
                        dots: rMediumDots ? true : false
                    },
                    1200: {
                        items: rLarge ? rLarge : 5,
                        nav: rLargeNav ? true : false,
                        dots: rLargeDots ? true : false
                    },
                    1240: {
                        items: rExtraLarge ? rExtraLarge : 5,
                        nav: rExtraLargeNav ? true : false,
                        dots: rExtraLargeDots ? true : false
                    }
                },
            });

            if (custom_nav) {
                var nav = $(custom_nav),
                    nav_next = $(".rt-next", nav),
                    nav_prev = $(".rt-prev", nav);

                nav_next.on("click", function (e) {
                    e.preventDefault();
                    owl.trigger('next.owl.carousel');
                    return false;
                });

                nav_prev.on("click", function (e) {
                    e.preventDefault();
                    owl.trigger('prev.owl.carousel');
                    return false;
                });
            }
        });
    });
});
