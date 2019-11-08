jQuery(document).ready(function ($) {
    $('form').on('submit', function (e) {
        var evt = e || window.event;
        var a = $(this);

        a.dynamicModal({
            title: '<div class="centered ui indeterminate text loader active inline">Preparing Files</div>',
            primary: null,
            secondary: null,
            cursorPosY: evt.clientY,
            cursorPosX: evt.clientX
        });
    });
});
