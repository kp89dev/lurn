$(() => {

    /**
     * Setup the carousel.
     */

    let carousel = $('#carousel');
    let carouselImages = carousel.find('img');
    let imagesToLoad = carouselImages.length;

    carouselImages.each(function () {
        let img = $(this);
        img.attr('src', img.data('src'));
        img.on('load', () => --imagesToLoad || startCarousel());
    });

    function startCarousel () {
        let currentHScroll = 0;

        function scroll () {
            let firstImage = carousel.find('img:first');
            let imageWidth = firstImage.width();

            carousel.css('margin-left', --currentHScroll);

            if (Math.abs(currentHScroll) > imageWidth) {
                let clone = firstImage.clone();
                let nextImageClone = firstImage.next('img').clone();

                firstImage.remove();
                currentHScroll += imageWidth;
                carousel.append(clone);

                carousel.css('margin-left', currentHScroll);

                switchHeaderImage(nextImageClone);
            }

            setTimeout(scroll, 20);
        }

        scroll();
    }

    let headerImage = $('#header .image');

    function switchHeaderImage (nextImage) {
        let lastImage = headerImage.find('img').last();

        headerImage.prepend(nextImage);
        lastImage.addClass('fade-out');

        setTimeout(() => lastImage.remove(), 2e3);
    }

    /**
     * Handle the header Entrepreneurs animation.
     */

    function selfTyped(el, o) {
        o.text || (o.text = el.text());

        let chars = o.text.split(''),
            currentText = '',
            interval,
            pos = 0,
            textWrapper = el;

        if (o.caret) {
            el.html('<span class="t-e-x-t"></span><span class="caret">&nbsp;</span>');
            textWrapper = el.find('.t-e-x-t');
        }

        interval = setInterval(() => {
            if (pos >= o.text.length) {
                clearInterval(interval);
                el.html(currentText);
                o.done && o.done();
            } else {
                setTimeout(() => {
                    currentText += chars[pos++] || '';
                    textWrapper.text(currentText);
                }, 10 + Math.ceil(Math.random() * 20));
            }
        }, o.speed || 110);
    }

    let animationBlock = $('[data-write]');
    let animationBlockText = animationBlock.data('write');

    // Set the container width.
    selfTyped(animationBlock, { text: animationBlockText });

    /**
     * Handle the testimonials.
     */

    let borderColors = ['#ffc487', '#a1ea73', '#73d8ff', '#a8a7ff', '#fba5ad', '#efe246'];
    let testimonials = $('#testimonials');
    let quotes = testimonials.find('ul.quotes');
    let timeout;

    Array.prototype.rand = function () {
        return this[Math.floor(Math.random() * this.length)];
    };

    function getPrevQuote (quote) {
        let prevQuote = quote.prev('li');
        return prevQuote.length ? prevQuote : quotes.find('> li').last();
    }

    function getNextQuote (quote) {
        let nextQuote = quote.next('li');
        return nextQuote.length ? nextQuote : quotes.find('> li').first();
    }

    function showNextQuote (i) {
        let quote = typeof i === 'undefined' ? getNextQuote(quotes.find('> li.focus')) : quotes.find('> li').eq(i);
        let prevQuote = getPrevQuote(quote);

        // Reset the classes.
        quotes.find('> li').removeClass('prev focus');
        prevQuote.addClass('prev');
        quote.addClass('focus');

        // Focus the appropriate floating picture.
        testimonials.find('> .floating-picture.focus').removeClass('focus');
        testimonials.find('> .floating-picture').eq(quote.data('index')).addClass('focus');

        // Change the border color.
        quote.css('border-top-color', borderColors.rand());

        // Automatically show the next quote.
        timeout = setTimeout(showNextQuote, Math.max(2e3, Math.min(4e3, quote.find('.text').text().length * 30)));
    }

    timeout = setTimeout(showNextQuote, 3e3);

    // Create the floating pictures.
    let quoteItems = quotes.find('> li');
    let height = 500;
    let rows = Math.ceil(quoteItems.length / 2);
    let photoHeight = 50;
    // Compute the vertical space between each element in the available height.
    let itemSpacing = (height - 120 - (rows * photoHeight)) / (rows - 1);
    let hMargin = Math.max(60, window.innerWidth / 2 - 500);

    quoteItems.each(function (i) {
        let quote = $(this);
        let author = quote.find('.author').text().trim();
        let imgSrc = quote.find('.author img').attr('src');
        let row = Math.ceil((i + 1) / 2);
        let shouldDistance = row % 1 === 0;
        let style = {};

        quote.attr('data-index', i);

        // Determine the side that the image is going to live on.
        i % 2 === 0
            ? (style.left = hMargin + (shouldDistance ? itemSpacing + Math.random() * itemSpacing * 2 : 0))
            : (style.right = hMargin + (shouldDistance ? itemSpacing + Math.random() * itemSpacing * 2 : 0));

        // Determine the position from the top of the container.
        style.top = 60 + ((row - 1) * (photoHeight + itemSpacing));

        let img = $('<img class="floating-picture' + (i === 0 ? ' focus' : '') + '" src="' + imgSrc + '" data-person="' + author + '">');
        img.css(style);

        let randomSize = photoHeight + (Math.random() * 3) * 10;
        img.css({
            width: randomSize,
            height: randomSize,
            animationDuration: (6 + Math.floor(Math.random() * 6)) + 's',
        });

        testimonials.append(img);

        img.on('click', function () {
            clearTimeout(timeout);
            showNextQuote(i);
        });
    });

    /**
     * Handle the scrolling process.
     */

    let counters = $('#counters');
    let counterItems = [];

    counters.find('.counter').each(function (i) {
        let value = parseInt($(this).find('.value').text().replace(/[^0-9]/g, ''));

        counterItems[i] = {
            value: value,
            incrBy: Math.round(value / 500) + 1
        };

        $(this).find('.value').text('0');
    });

    function handleCounters () {
        let items = counters.find('.counter');

        items.each(function (i) {
            let top = counters.offset().top;

            if (! counterItems[i].done && top < innerHeight + scrollY) {
                counterItems[i].done = true;
                increaseNumber(0, counterItems[i]);
            }

            function increaseNumber(n, counterItems) {
                let counter = $('.counter').eq(i);

                if (n >= counterItems.value) {
                    counter.find('.value').text(formatNumber(counterItems.value));
                    counter.find('.bar').addClass('done');
                } else {
                    counter.find('.value').text(formatNumber(n + counterItems.incrBy));
                    counter.find('.bar').css({
                        width: (Math.min(counterItems.value, n) / counterItems.value * 100) + '%'
                    });

                    setTimeout(() => increaseNumber(n + counterItems.incrBy, counterItems), 1);
                }
            }
        });
    }

    const topBar = $('#top-bar');

    function handleTopNav () {
        (window.scrollY > 50)
            ? topBar.addClass('fixed')
            : topBar.removeClass('fixed');
    }

    function handleScrolling () {
        handleCounters();
        handleTopNav();
    }

    handleScrolling();
    $(window).scroll(handleScrolling);

    /**
     * Handle the parallax effect.
     */

    let parallaxElements = [];

    $('[parallax-image]').each(function () {
        let w = $(this),
            img = w.attr('parallax-image');

        w.prepend('<img class="parallax" src="' + img + '">');

        parallaxElements.push(w);
    });

    $(window).scroll(() => {
        let st = window.scrollY + 400, el, diff, img;

        for (let i = 0; i < parallaxElements.length; i++) {
            el = parallaxElements[i];
            img = el.find('> img.parallax').first();

            if (st > el[0].offsetTop) {
                diff = (st - el[0].offsetTop) / 2;
                img.css('margin-top', -diff + 'px');
            } else {
                img.css('margin-top', 0);
            }
        }
    });

});

function formatNumber(n) {
    return n.toString().replace(/./g, function(c, i, a) {
        return i && c !== "." && ((a.length - i) % 3 === 0) ? ',' + c : c;
    });
}

new Vue({
    el: '#post-container',

    data: {
        posts: []
    },

    methods: {
        getBlogPosts() {
            $.getJSON('/blog/laravel-feed?_format=hal_json', posts => posts && (this.posts = posts));
        },
    },

    mounted() {
        this.getBlogPosts();
    }
});