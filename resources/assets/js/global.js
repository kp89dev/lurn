jQuery($ => {
    // Execute a keep-alive request to the app every 110 minutes to keep things like the CSRF token alive.
    setInterval(() => $.get('/api/unread-news'), 1e3 * 60 * 110);

    // Activate the data-titles on the rewards widget.
    $('.rewards-widget-steps [data-title]').each(function () {
        $(this).popup({
            html: $(this).data('title'),
            variation: 'inverted',
            position: 'left center',
        });
    });

    // Handle the .courses' hover boxes.
    $('.course + .hover-box').each(function () {
        let hoverBox = $(this);
        let course = hoverBox.prev('.course');

        course.popup({
            html: hoverBox.html().trim(),
            className: { popup: 'ui popup course-hover-box' },
            position: hoverBox.data('position'),
            hoverable: true,
            onShow: function () {
                return $(window).width() > 768;
            }
        });

        hoverBox.remove();
    });
});
