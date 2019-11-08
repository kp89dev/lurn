/**
 * @file
 * Lurnblog Header.
 */
(function ($, _) {
    var preNav = $('#pre_navigation');
    var navbar = $('#navbar');
    var preNavHeight = preNav.height();
    var minTop = parseInt(navbar.css('top'),10);
    var addTop = preNavHeight + minTop;
    if ($(window).scrollTop() == 0 && $(window).width() > 768) {
        navbar.css({ top: addTop });
    }
    $(window).scroll(function () {
        var st = $(this).scrollTop();
        if (st > minTop){
            navbar.css({ top: minTop });
        }else if($(window).width() > 768){
            navbar.css({ top: addTop - st });
        }
    });
})(window.jQuery, window._);
