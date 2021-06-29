jQuery(function($) {

    $(document).ready(function() {
        // Header Slider
        var owlMainSlider = $('.header-slider');
        owlMainSlider.owlCarousel({
            rtl:$("html").attr("dir") == 'rtl' ? true : false,
            items: 1,
            loop: true,
            dots: false,
            nav: true,
            navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
            autoplay: false,
            smartSpeed: 1000,
            responsive: {
                0: {},
                768: {},
                992: {}
            }
        });
        // Header Slide items with animate.css    
        owlMainSlider.owlCarousel();
        owlMainSlider.on('translate.owl.carousel', function (event) {
            var data_anim = $("[data-animation]");
            data_anim.each(function() {
                var anim_name = $(this).data('animation');
                $(this).removeClass('animated ' + anim_name).css('opacity', '0');
            });
        });
        $("[data-delay]").each(function() {
            var anim_del = $(this).data('delay');
            $(this).css('animation-delay', anim_del);
        });
        $("[data-duration]").each(function() {
            var anim_dur = $(this).data('duration');
            $(this).css('animation-duration', anim_dur);
        });
        owlMainSlider.on('translated.owl.carousel', function() {
            var data_anim = owlMainSlider.find('.owl-item.active').find("[data-animation]");
            data_anim.each(function() {
                var anim_name = $(this).data('animation');
                $(this).addClass('animated ' + anim_name).css('opacity', '1');
            });
        });

        $(".testimonial-content").owlCarousel({
            nav: true,
        	navText: ['<i class="fa fa-long-arrow-left"></i>', '<i class="fa fa-long-arrow-right"></i>'],
            items: 1,
            margin: 1,
            dots: false,
            linked: ".testimonial-thumb"
        });
        var sync2 = $(".testimonial-thumb");
        $(sync2).owlCarousel({
            loop: true,
            dots: false,
            items: 3,
            center: true,
            nav: false,
            margin: 10,
            linked: sync2.prev()
        }).on('initialized.owl.carousel linked.to.owl.carousel', function() {
            sync2.find('.owl-item.current').removeClass('current');
            var current = sync2.find('.owl-item.active.center').length ? sync2.find('.owl-item.active.center') : sync2.find('.owl-item.active').eq(0);
            current.addClass('current');
        });

        /* --------------------------------------
            Scroll UP
        -------------------------------------- */

        $(window).on('scroll', function() {
            if ($(this).scrollTop() > 100) {
                $('.scrollup').fadeIn();
            } else {
                $('.scrollup').fadeOut();
            }
        });

        $('.scrollup').on('click', function() {
            $("html, body").animate({
                scrollTop: 0
            }, 600);
            return false;
        });

        // Search
        var changeClass = function(name) {
            $('#search').removeAttr('class').addClass(name);
        }

        /*------------------------------------
            Search
        --------------------------------------*/

        var submitIcon = $('.sb-icon-search');
        var submitInput = $('.sb-search-input');
        var searchBox = $('.sb-search');
        var isOpen = false;

        submitIcon.on('mouseup', function() {
            return false;
        });

        searchBox.on('mouseup', function() {
            return false;
        });

        submitIcon.on('click', function() {
            if (isOpen == false) {
                searchBox.addClass('sb-search-open');
                isOpen = true;
                submitInput.focus();
            } else {
                searchBox.removeClass('sb-search-open');
                isOpen = false;
            }
        });
        
        /*------------------------------------
            Cart
        --------------------------------------*/

        function overlayToggle() {
            if ($('.cart-overlay').hasClass('active')) {
                $('.cart-overlay').removeClass('active');
            } else {
                $('.cart-overlay').addClass('active');
            }
        }
        $('.cart--open, .cart-overlay, .close-sidenav').on('click', function(e) {
            var $sidecart = $('.sidenav.cart');
            if ($sidecart.hasClass('active')) {
                $sidecart.removeClass('active');                
            } else {
                $sidecart.addClass('active');
            }
            //alert('Click the OK button Now !');
            overlayToggle();
            e.preventDefault();
        });

        /*------------------------------------
            Sticky Menu
        --------------------------------------*/
        var $window       = $( window );
        var lastScrollTop = 0;
        var $header       = $( '.sticky-nav' );
        var headerBottom  = $header.position().top + $header.outerHeight( true );

        $window.scroll( function() {

            var windowTop  = $window.scrollTop();

            // Add custom sticky class 
            if ( windowTop >= headerBottom ) {
                $header.addClass( 'is-sticky' );
                $header.addClass( 'swingOutX' );
                $header.removeClass( 'swingInX' );
            } else {
                $header.removeClass( 'is-sticky' );
                $header.removeClass( 'show' );
                $header.removeClass( 'swingOutX' );
                $header.addClass( 'swingInX' );
            }

            // Show/hide
            if ( $header.hasClass( 'is-sticky' ) ) {
                if ( windowTop <= headerBottom || windowTop < lastScrollTop ) {
                    $header.addClass( 'show' );
                    $header.addClass( 'swingInX' );
                    $header.removeClass( 'swingOutX' );
                } else {
                    $header.removeClass( 'show' );
                    $header.removeClass( 'swingInX' );
                    $header.addClass( 'swingOutX' );
                }
            }

            lastScrollTop = windowTop;

        } );

        /*------------------------------------
            jQuery MeanMenu
        --------------------------------------*/
        $('.mobile-menu-active').meanmenu({
            meanScreenWidth: "991",
            meanMenuContainer: '.mobile-menu'
        });

        /* last  2 li child add class */
        $('ul.menu > li').slice(-2).addClass('last-elements');

        // Add/Remove .focus class for accessibility
        $('.main-menu').find( 'a' ).on( 'focus blur', function() {
            $( this ).parents( 'ul, li' ).toggleClass( 'focus' );
        });
    });

    function processAutoheight() {
        var maxHeight = 0;

        // This will check first level children ONLY as intended.
        $(".navbar-area .navigation:not(.cr-dropdown-menu)").each(function(){

            height = $(this).outerHeight(true); // outerHeight will add padding and margin to height total
            if (height > maxHeight ) {
                maxHeight = height;
            }
        });

        $(".navbar-area").css('min-height', maxHeight);
    }

    // Recalculate under any condition that the viewport dimension has changed
    $(document).ready(function() {

        $(window).resize(function() { processAutoheight(); });

        // BOTH were required to work on any device "document" and "window".
        // I don't know if newer jQuery versions fixed this issue for any device.
        $(document).resize(function() { processAutoheight(); });

        // First processing when document is ready
        processAutoheight();
    });

    $(document).ready(function() {
        var formFields = $('.input-hantus');
        formFields.each(function(){
            var field = $(this);
            var input = field.find('input, textarea');
            var label = field.find('label span, label');            
            function checkInput() {
                var valueLength = input.val().length;
                if (valueLength >= 0) {
                    input.addClass('_2Pfbi');
                    label.addClass('_2tL9P');
                } 
                else{
                    input.removeClass('_2Pfbi');
                    label.removeClass('_2tL9P');
                }
            }
        
            input.focus(function() {
                input.addClass('_2Pfbi');
                label.addClass('_2tL9P');
                checkInput()
            })
            input.focusout(function() {
                 checkInput();
                if(input.val().length<= 0){
                    input.removeClass('_2Pfbi');
                    label.removeClass('_2tL9P');
                }
            })
        });
    });

    $(document).ready(function() {
        var formFields = $('.search-form');
        formFields.each(function(){
            var field = $(this);
            var input = field.find('label input');
            var label = field.find('label span');            
            function checkInput() {
                var valueLength = input.val().length;
                if (valueLength >= 0) {
                    input.addClass('_2Pfbi');
                    label.addClass('_2tL9P');
                } 
                else{
                    input.removeClass('_2Pfbi');
                    label.removeClass('_2tL9P');
                }
            }        
            input.focus(function() {
                input.addClass('_2Pfbi');
                label.addClass('_2tL9P');
                checkInput()
            })
            input.focusout(function() {
                 checkInput();
                if(input.val().length<= 0){
                    input.removeClass('_2Pfbi');
                    label.removeClass('_2tL9P');
                }
            })
        });
    });

    $(document).ready(function() {
        var formFields = $('[class*="comment-form-"], .woocommerce-product-search');
        formFields.each(function(){
            var field = $(this);
            var input = field.find('input, textarea');
            var label = field.find('label');            
            function checkInput() {
                var valueLength = input.val().length;
                if (valueLength >= 0) {
                    input.addClass('_2Pfbi');
                    label.addClass('_2tL9P');
                } 
                else{
                    input.removeClass('_2Pfbi');
                    label.removeClass('_2tL9P');
                }
            }        
            input.focus(function() {
                input.addClass('_2Pfbi');
                label.addClass('_2tL9P');
                checkInput()
            })
            input.focusout(function() {
                 checkInput();
                if(input.val().length<= 0){
                    input.removeClass('_2Pfbi');
                    label.removeClass('_2tL9P');
                }
            })
        });
    });

});