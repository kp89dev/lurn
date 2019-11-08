window.showLoadScreen = function (nicheId) {
    $('.niche-design .wrapper').hide();
    $('.page-content-wrapper').show();

    /**
     * The load screen zone.
     */
    var loadScreen = $('.niche-design.load-screen');

    if (loadScreen.length) {
        function redirectFromLoadScreen () {
            window.location = '/tools/niche-detective/niche/' + nicheId;
        }

        var message = loadScreen.find('.message');
        var pb = loadScreen.find('.progressbar');
        var pb_seconds = 0;
        var wait_seconds = 5;
        var text_seconds;

        history.pb_seconds = 0;
        pb.progressbar();

        var interval = setInterval(function () {
            if (pb_seconds > wait_seconds) {
                clearInterval(interval);
                redirectFromLoadScreen();
                return;
            }

            if (history.pb_seconds !== parseInt(pb_seconds)) {
                text_seconds = wait_seconds - parseInt(pb_seconds);
                message.text(text_seconds + ' second' + (text_seconds === 1 ? '' : 's') + ' while we gather your info');
            }

            loadScreen.find('.progressbar .ui-progressbar-value')
                .show()
                .css('width', (pb_seconds / wait_seconds * 100) + '%');

            pb_seconds += .1;
        }, 100);
    }
};

window.createNichesHtml = function (niches) {
    var html = "";
    $.each(niches, function (k, niche) {
        html += "<div class='niche-container'><a class='btn btn-hg btn-lg btn-niche btn-block' onclick='showLoadScreen(" + niche.id + ")'>" + niche.label + "</a></div>";
    });

    return html;
};

window.openNicheCategory = function (el, categoryId) {
    $('.niches').slideUp();
    var $el = $(el);
    var container = $el.parent().siblings('.niches');
    $('.blue-bg').removeClass('blue-bg');
    $($el).addClass('blue-bg');

    if (container.html()) {
        container.slideDown();
        return;
    }

    container.html('<img src="/assets/global/img/loading-horizontal.gif" alt="Loading">');
    container.slideDown();

    $.ajax({
        type: 'POST',
        url: '/tools/niche-detective/get-niche-categories',
        success: function (result) {
            if (result.status == "0") {
                var html = createNichesHtml(result.niches);
                container.html(html);
            } else {
                alert('Unable to get the niches. Please try again');
            }
        },
        data: { id: categoryId, _token: $('input[name="_token"]').val() },
        dataType: 'json'
    });
};
