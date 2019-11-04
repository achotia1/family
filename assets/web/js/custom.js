// remove cart popup on outside pop up click
$(document.body).click(function(evt)
{
    var clicked = evt.target;
    var cart = $(clicked).closest('.cart');  
    if(cart.length === 0)
    {
        if($('.cart-box').hasClass('active'))
        {
            $('.cart-box').removeClass('active')   
        }
    }
})

// document ready 
$(document).ready(function () {
    //console.log("test");
    //var href = document.location.href.match(/[^\/]+$/)[0];
    var href = document.location.href.match(/[^\/]+$/);
    if (href != null) {
        href = href[0];
    }
    // console.log(href);
    //console.log("test1212121");
    // console.log(href);
    //if (href == 'Home-Default.php') {
    if (href == null) {
        $('header').addClass('inner');
        $(window).scroll(function () {
            if ($(window).scrollTop() >= 700) {
                $('header').addClass("sticky");
            } else {
                $('header').removeClass("sticky");
            }
        });
    } else {
        $('header').removeClass('inner');
    }

    //console.log("test1");

    // Homepage Sticky Navigaiton 
    //if ((document.location.href.match(/[^\/]+$/)[0]) != 'Home-Default.php') {
    if (href != null) {
        $("header").addClass("sticky");
        $(".main-wrapper").css("margin-top", "85px");

    }
    /* if (href == null) {
         $(window).scroll(function() {
             console.log($(this).scrollTop());
             if ($(window).scrollTop() >= 170) {
                 $('header').addClass("sticky");
             } else {
                 $('header').removeClass("sticky");
             }
         });
     }*/
    // State Page Trangle Shape
    // console.log($(window).width()/2);
    // var screen_Width = $(window).width()/2;
    // $('.bottom-trangle').append('<span class="trangle-shape"><span>');
    // $('.trangle-shape').css({
    //     'border-left' : screen_Width+'px solid transparent',
    //     'border-right' : screen_Width+'px solid transparent'
    // });


    // Cart Js
    $('#cart-close img').on('click', function () {
        // console.log('clicked');
        $('.cart-box').removeClass('active');
    });

    // Cart Basket Open
    $('.login-cart .cart .btn').click(function () {
        $('.cart-box').toggleClass('active');
        $('.top-square-cart').toggleClass('active');
        $('body').toggleClass('stop');
    });

    $("#other-checkbox").click(function () {
        $("#other-checkbox-input").toggleClass('active');
    });


    // Navigation Open
    $('.toggle-bar').click(function () {
        $('nav').toggleClass('active');
        $('.main-wrapper').toggleClass('open-menu');
        $('header').toggleClass('open-menu');
        $('.res-menu-close').toggleClass('active');
        $('body').toggleClass('stop');
    });
    // Navigation Close
    $(".menu-close, .res-menu-close, .login-cart .cart .btn, .open-menu").click(function () {
        $('nav').removeClass('active');
        $('.main-wrapper').removeClass('open-menu');
        $('header').removeClass('open-menu');
        $('.res-menu-close').removeClass('active');
        $('body').removeClass('stop');
    });

    // menu on hover
    // $('nav ul.menu li.drop-menu').hover(function() {
    //   $(this).find('ul.sub-menu').stop(true, true).delay(0).fadeIn(400);
    // }, function() {
    //   $(this).find('ul.sub-menu').stop(true, true).delay(0).fadeOut(230);
    // });
    //console.log("test3");

    // Select Box 
    $(function () {
        $("#online-course-head, #stae_drop_head, #state_drop_bottom,.school_admitted,#credit-in, #online-landing, #my-profile-country, #my-profile-state, #my-profile-card-month, #my-profile-card-year, .myselect").selectbox();
    });

    (function ($) {
        $(window).on("load", function () {
            $(".sbOptions").addClass('mCustomScrollbar');
            $(".sbOptions").mCustomScrollbar();
        });
    })(jQuery);

    // Icons Images Hover Changes Js
    $('nav ul.menu li.drop-menu ul.sub-menu li, .hover-me').mouseenter(function () {
        var test = $(this).find("span img").attr('src');
        $(this).find("span img").attr('org-path', test);
        //console.log(test);
        var tarr = test.split('/');
        var file = tarr[tarr.length - 1];
        var filename = file.split('.')[0];
        var checking = filename.split('-')[1];
        if (checking === 'active') {
            $(this).find("span img").attr('src', test.replace(filename, filename));
        } else {
            var hoverfile = filename + '-active';
            $(this).find("span img").attr('src', test.replace(filename, hoverfile));
        }
    });

    $('nav ul.menu li.drop-menu ul.sub-menu li, .hover-me').mouseleave(function () {
        var orgpath = $(this).find("span img").attr('org-path');
        //console.log(orgpath);
        $(this).find("span img").attr('src', orgpath).removeAttr('org-path');

    });

    $('#client-slider').owlCarousel({
        loop: true,
        margin: 10,
        responsiveClass: true,
        nav: true,
        navText: ['<img src="assets/web/images/icons/testimonial_arrow_left_lg.png">', '<img src="assets/web/images/icons/testimonial_arrow_right_lg.png">'],
        autoplay: false,
        autoplayTimeout: 5000,
        responsive: {
            0: {
                items: 1,
                stagePadding: 0,
                margin: 0,
            },
            768: {
                items: 1,
                stagePadding: 0,
                margin: 0,
            },
            1025: {
                items: 1,
                stagePadding: 220,
                margin: 30,
            },
            1200: {
                items: 1,
                margin: 10,
                stagePadding: 250,
            },
            1340: {
                items: 1,
                margin: 10,
                stagePadding: 280,
            }
        }
    });

    // Announcement
    $('.announcement #close').click(function () {
        $('.announcement').addClass('active');
        $('.main-wrapper').addClass('active');
        $('header').css({
            'top': '0px'
        });

        $(".header,.inner").css("padding", "31px 0px 16px");

        var nDays = 999;
        var cookieName = $(this).parent('div').attr('id');
        var cookieValue = "true";

        var today = new Date();
        var expire = new Date();
        expire.setTime(today.getTime() + 3600000 * 24 * nDays);
        document.cookie = cookieName + "=" + escape(cookieValue) + ";expires=" + expire.toGMTString() +
            ";path=/";
        if ($(window).width() > 768) {
            if ($(".desktop-header").hasClass('inner')) {
                $(".main-wrapper").css('margin-top', 0);
            } else {
                $(".main-wrapper").css('margin-top', '55px');
            }
        }
        else {
            $(".main-wrapper").css('margin-top', $(".desktop-header.sticky").height());
        }
    });

    // jQuery(document).ready(function($){
    //     $('.demo1').dsCountDown({
    //     endDate: new Date("December 24, 2020 23:59:00")
    //     });
    // });

    $('#menu-box li a, .menu-links li a, .back-top').on('click', function (event) {
        $('#menu-box li a::before').addClass('active');
        var target = $($(this).attr('href'));
        if (target.length) {
            event.preventDefault();
            $('html, body').animate({ scrollTop: target.offset().top - 110 }, 600);
        }
    });


    jQuery('#menu-box li').click(function () {
        $("#menu-box li").removeClass('active');
        $(this).addClass('active');
    });

    // Courses Menu List Sticky
    function sticky_relocate() {
        var menuHeigth = ($('#menu-box').height() / 2);
        var window_top = $(window).scrollTop();
        var stickyoffset = $('#sticky-anchor').offset();
        if (stickyoffset != null) {
            var div_top = ($('#sticky-anchor').offset().top) - menuHeigth;
            if (window_top + 110 > div_top) {
                $('#menu-box').addClass('stick');
            } else {
                $('#menu-box').removeClass('stick');
            }
        }

    }

    $(function () {
        $(window).scroll(sticky_relocate);
        sticky_relocate();
    });

    // Upload Btn Js
    $(document).on('change', '.up', function () {
        var names = [];
        var length = $(this).get(0).files.length;
        for (var i = 0; i < $(this).get(0).files.length; ++i) {
            names.push($(this).get(0).files[i].name);
        }
        // $("input[name=file]").val(names);
        if (length > 2) {
            var fileName = names.join(', ');
            $(this).closest('.form-group').find('.form-control').attr("value", length + " files selected");
        } else {
            $(this).closest('.form-group').find('.form-control').attr("value", names);
        }
    });

    // My Account Menu List Sticky
    $('#Account-Links').scrollFix({
        side: 'top',
        topPosition: 100
    });


    // var $ = jQuery;
    // window.addEventListener("resize", myFunction);


    // function myFunction() {
    //     var screenWidthCurr = $(document).width();
    //     if (screenWidthCurr > 992) {
    //         jQuery(".account-links").addClass("scrollfix-top");
    //     } else {
    //         jQuery(".account-links").removeClass("scrollfix-top");
    //     }
    // } 
});


if (navigator.userAgent.search("MSIE") >= 0) {
    $('html').addClass('MSIE');
} else if (navigator.userAgent.search("Safari") >= 0 && navigator.userAgent.search("Chrome") < 0) {
    $('html').addClass('Safari');
} else if (navigator.userAgent.search("Opera") >= 0) {
    $('html').addClass('Opera');
} else if (navigator.platform.search("MacIntel") >= 0 && navigator.userAgent.search("Chrome") >= 0) {
    $('html').addClass('Macchrome');
} else if (navigator.userAgent.search("Chrome") >= 0) {
    $('html').addClass('Chrome');
} else if (navigator.platform.search("MacIntel") >= 0 && navigator.userAgent.search("Firefox") >= 0) {
    $('html').addClass('MacFirefox');
} else if (navigator.userAgent.search("Firefox") >= 0) {
    $('html').addClass('Firefox');
}