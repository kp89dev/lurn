new Vue({
    el: '#mega-menu',
    data: {
        menu: menuData,
        course: menuData[0]
    }
});

$(function() {
    var bc = $('#browser-courses'),
        dd = bc.find('> .dropdown');

    dd.dropdown('show').dropdown('hide');
    bc.addClass('hidden');
    setTimeout(() => bc.removeClass('hidden'), 600);

    // Disable parent scrolling for the following elements.
    $('#courses ul, #preview').each(function () {
        var elem = $(this),
            height = elem[0].offsetHeight,
            scrollHeight = elem[0].scrollHeight;

        elem.bind('mousewheel', (e, d) => {
            let st = this.scrollTop, h = scrollHeight - height;

            if ((st === h && d < 0) || (st === 0 && d > 0)) {
                e.preventDefault();
            }
        });
    });

    $('.ui.dropdown').each(function () {
        $(this).dropdown();
    });

    $('.ui.accordion').each(function () {
        $(this).accordion();
    });
});