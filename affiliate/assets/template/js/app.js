$('.menu-button').click(function(){
  $('body').toggleClass('mobile-show');  
});

$('.overly').click(function(){   
  $('body').removeClass('mobile-show');  
});

!function ($) {
    "use strict";

    $(".main .toggle-bar").on("click",function(){
        if($(".main .dashboard-wrap").hasClass("active")){
            localStorage.removeItem('close-sidebar');
            
            $(".main .left-menu").removeClass("deactive");
            $(".main .logo").removeClass("deactive");
            $(".main .dashboard-wrap").removeClass('active');
        } else {
            localStorage.setItem('close-sidebar','active');

            $(".main .left-menu").addClass("deactive");
            $(".main .logo").addClass("deactive");
            $(".main .dashboard-wrap").addClass('active');
        }
    })

    

    var MainApp = function () {
        this.$body = $("body"),
            this.$wrapper = $("#wrapper"),
            this.$btnFullScreen = $("#btn-fullscreen"),
            this.$leftMenuButton = $('.button-menu-mobile'),
            this.$menuItem = $('.has_sub > a')
    };
    //scroll
    MainApp.prototype.initSlimscroll = function () {
        $('.slimscrollleft').slimscroll({
            height: 'auto',
            position: 'right',
            size: "10px",
            color: '#9ea5ab'
        });
    },
        //left menu
        MainApp.prototype.initLeftMenuCollapse = function () {
            var $this = this;
            this.$leftMenuButton.on('click', function (event) {
                event.preventDefault();
                $this.$body.toggleClass("fixed-left-void");
                $this.$wrapper.toggleClass("enlarged");
            });
        },
        //left menu
        MainApp.prototype.initComponents = function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl);
            });
        },

        //full screen
        MainApp.prototype.initFullScreen = function () {
            var $this = this;
            $this.$btnFullScreen.on("click", function (e) {
                e.preventDefault();

                if (!document.fullscreenElement && /* alternative standard method */ !document.mozFullScreenElement && !document.webkitFullscreenElement) {  // current working methods
                    if (document.documentElement.requestFullscreen) {
                        document.documentElement.requestFullscreen();
                    } else if (document.documentElement.mozRequestFullScreen) {
                        document.documentElement.mozRequestFullScreen();
                    } else if (document.documentElement.webkitRequestFullscreen) {
                        document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
                    }
                } else {
                    if (document.cancelFullScreen) {
                        document.cancelFullScreen();
                    } else if (document.mozCancelFullScreen) {
                        document.mozCancelFullScreen();
                    } else if (document.webkitCancelFullScreen) {
                        document.webkitCancelFullScreen();
                    }
                }
            });
        },
        //full screen
        MainApp.prototype.initMenu = function () {
            var $this = this;
            $this.$menuItem.on('click', function () {
                var parent = $(this).parent();
                var sub = parent.find('> ul');

                if (!$this.$body.hasClass('sidebar-collapsed')) {
                    if (sub.is(':visible')) {
                        sub.slideUp(300, function () {
                            parent.removeClass('nav-active');
                            $('.body-content').css({height: ''});
                            adjustMainContentHeight();
                        });
                    } else {
                        visibleSubMenuClose();
                        parent.addClass('nav-active');
                        sub.slideDown(300, function () {
                            adjustMainContentHeight();
                        });
                    }
                }
                return false;
            });

            //inner functions
            function visibleSubMenuClose() {
                $('.has_sub').each(function () {
                    var t = $(this);
                    if (t.hasClass('nav-active')) {
                        t.find('> ul').slideUp(300, function () {
                            t.removeClass('nav-active');
                        });
                    }
                });
            }

            function adjustMainContentHeight() {
                // Adjust main content height
                var docHeight = $(document).height();
                if (docHeight > $('.body-content').height())
                    $('.body-content').height(docHeight);
            }
        },
        MainApp.prototype.activateMenuItem = function () {
            // === following js will activate the menu in left side bar based on url ====
             $(".navbar-nav a").each(function () {
                
                if (this.href == window.location.href) {
 
                    $(this).addClass('active');

                    $(this).parents('.nav-item').addClass('show');
                  
                    if($(this).parent().attr('data-menu')=='tutorialnav')
                    {
                       $(this).parents('.dropdown .show').find('.nav-link').trigger('click');
                        $('#submenu1').addClass('show'); 
                    }
                    else
                        $(this).parents('.dropdown').find('.nav-link').trigger('click');
                }
                else
                {
                   
                 
                }
            });
        },
        MainApp.prototype.Preloader = function () {
            $( window ).on( "load", function(){
                $('#status').fadeOut();
                $('#preloader').delay(350).fadeOut('slow');
                $('body').delay(350).css({
                    'overflow': 'visible'
                });
            });
        },
        MainApp.prototype.init = function () {
            this.initSlimscroll();
            this.initLeftMenuCollapse();
            this.initComponents();
            this.initFullScreen();
            this.initMenu();
            this.activateMenuItem();
            this.Preloader();
        },
        //init
        $.MainApp = new MainApp, $.MainApp.Constructor = MainApp
}(window.jQuery),

//initializing
    function ($) {
        "use strict";
        $.MainApp.init();
    }(window.jQuery);
