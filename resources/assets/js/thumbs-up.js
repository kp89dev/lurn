$(() => {

    /*tippy('.thumbs-up', {
        placement:      'top',
        size:           'large',
        animation:      'shift-toward',
        duration:       150,
        dynamicTitle:   true,
        arrow:          true,
        arrowTransform: 'scale(0.8)'
    });*/

    var shorterNumber = function (number) {
        if (number >= 1e6) {
            return Math.round(number / 1e6) + 'M';
        }

        if (number >= 1e3) {
            return Math.round(number / 1e3) + 'K';
        }

        return number;
    };

    $(document).on('click', '.thumbs-up', (e) => {
        e.preventDefault();
        e.stopPropagation();

        var $this      = $(e.currentTarget);
        var $container = $this.parent('.thumbs-up-container');
        var $count     = $('.thumbs-up-counter', $container);
        var amount     = parseInt($count.data('count'));
        var likes      = 0;

        if (!$this.data('user-id')) {
            return false;
        }

        $this.toggleClass('active');

        if ($this.hasClass('active')) {
            amount += 1;
            likes   = 1;
        } else {
            amount -= 1;
        }

        $('i', $container).toggleClass('outline');

        $count.text(shorterNumber(amount));
        $count.data('count', amount);
        $this.attr('title', shorterNumber(amount));

        $.post(
            '/api/click-thumbs-up',
            {
                course_id: $this.data('course-id'),
                user_id: $this.data('user-id'),
                likes: likes,
                page: window.location.href
            }
        )
    });

});