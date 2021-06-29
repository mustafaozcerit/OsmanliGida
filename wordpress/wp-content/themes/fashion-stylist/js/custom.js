jQuery(document).ready(function($){    
    
    var rtl, mrtl;
    
    if( fashion_stylist_data.rtl == '1' ){
        rtl = true;
        mrtl = false;
    }else{
        rtl = false;
        mrtl = true;
    }

    //banner layout two
    $('#banner-slider-two').owlCarousel({
        loop       : true,
        nav        : true,
        dots       : true,
        autoplay   : true,
        animateOut : '',
        navText    : '',
        rtl        : rtl,
        lazyLoad   : true,
        responsive : {
            1440: {
                margin: 40,
                items: 4
            },
            1025: {
                items: 3,
                margin: 40,
            },
            768: {
                items: 2,
                margin: 20
            },
            0: {
                items: 1,
                margin: 0
            }
        }
    });

    // $('.site-header .form-holder').prepend('<div class="btn-close-form"><span></span></div>');

    $('.btn-close-form').click(function(){
        $(this).parent('.form-holder').hide("fast");
    });

    $(window).keyup(function(event){
        if(event.key == 'Escape') {
            $('.form-holder').hide("fast");    
        }
    });
       
});