import Cookies from 'js-cookie';

const isMobile = /ipad|iphone|android/i.test(navigator.userAgent);

jQuery($ => {
    // Ensure that the navigation buttons are the same height.
    let prev = $('#course-navigation a.prev > div'),
        next = $('#course-navigation a.next > div'),
        maxHeight = Math.max(prev.length ? prev[0].offsetHeight : 20, next.length ? next[0].offsetHeight : 20);

    $('#course-navigation a > div').css('height', maxHeight);

    /**
     * Handle the sidebar toggling effect.
     */
    let open = $('#sidebar #chapters li.open');

    if (open.length) {
        open.each(function () {
            $(this).find('ul, ol').animate({ height: 'toggle' }, 1);
        });
    }

    $('#sidebar .chapter .module-toggle').on('click', function () {
        let li = $(this).parents('li');
        let child = li.find('ul, ol').first();

        child[0].offsetHeight ? li.removeClass('open') : li.addClass('open');
        child.stop().animate({ height: 'toggle' }, 200, function () {
            $(this)[0].offsetHeight ? li.addClass('open') : li.removeClass('open');
        });
    });

    if ($('#notes').length > 0) {
        let handleNotesPosition = function handleNotesPosition () {
            let ww = $(window).width();

            ww >= 1590 ? $('#notes').removeClass('inside') : $('#notes').addClass('inside');
        };

        new Vue({
            el: '#notes',
            data: { notes, course, lesson },
            methods: {
                saveNotes: function saveNotes () {
                    $.post('/api/notes/' + this.course, {
                        notes: this.notes,
                        lesson: this.lesson
                    });
                },
                modelNotes: function modelNotes (e) {
                    this.notes = e.target.value;
                }
            },
            watch: {
                notes: debounce(function () {
                    this.saveNotes();
                }, 300)
            }
        });

        /**
         * Handle the notes positioning.
         */
        $('#notes').on('click', '.toggle', function () {
            $('#notes').toggleClass('open');
        });

        $('#notes').on('click', 'h4', function () {
            $('#notes').hasClass('open') ? $('#notes textarea').focus() : $('#notes').addClass('open');
        });

        handleNotesPosition();
        $(window).resize(handleNotesPosition);
    }

    /**
     * Show the dialog which asks the user if the course is complete
     * when they click on one of the navigation buttons at the bottom.
     */
    $('.ask-if-complete, .silent-complete').on('click', function (e) {
        e.preventDefault();
        let evt = e || window.event;

        let a = $(this);

        if (a.hasClass('next') || a.hasClass('prev')) {
            a.addClass('loading');
        }

        let complete = function () {
            a.removeClass('loading');
        };

        let completeLesson = function () {
            let finish = function () {
                $.post('/api/complete-lesson/' + course, { lesson, link }, 'json')
                    .done(function (res) {
                        if (res.complete) {
                            $('#completed-course').modal('show');
                        } else {
                            window.location.href = a.attr('href');
                        }
                    }).always(complete);
            };

            if (link) {
                if (a.hasClass('sidebar-complete')) {
                    $.post('/api/complete-lesson/' + course, {
                            lesson: a.attr('lesson'),
                            sidebar: lesson,
                            link,
                    }, 'json').done(function (res) {
                            if (res.complete) {
                                $('#completed-course').modal('show');
                            } else {
                                window.location.href = a.attr('href');
                            }
                        }).always(complete);
                } else {
                    finish();
                }
            } else {
                finish();
            }
        };

        let newWin;
        let link = false;

        if (a.attr('target') === '_blank') {
            newWin = window.open(a.attr('href'));
            link = true;
        } else {
            newWin = window;
        }

        if (a.hasClass('ask-if-complete')) {
            if (completed === 0) {
                a.dynamicModal({
                    title: 'Do you want to mark this ' + (a.hasClass('mark-module') ? 'module' : 'lesson') + ' complete?',
                    primary: "Yes, I'm done",
                    secondary: 'No, skip for now',
                    primaryAction: completeLesson,
                    cursorPosY: evt.clientY,
                    cursorPosX: evt.clientX,
                    secondaryAction: function () {
                        window.location.href = a.attr('href');
                        complete();
                    },
                });
            } else {
                completeLesson();
            }
        } else {
            completeLesson();
        }
    });

    $('.introVideoCall').click(function (event) {
        event.preventDefault();
        let modalId = $(this).attr("data-target");
        let iframe = $(modalId + ' > .modal-dialog > .modal-content > .modal-body').find('iframe.iframeVideoId');
        let vidsrc = iframe.attr('src');

        iframe.attr('src', vidsrc + '?autoplay=true');
        $(modalId).addClass('modal-open');
        $('body').append('<div class="modal-backdrop fade in"></div>');
        $(modalId).show();
        $(modalId).find('.modal-dialog').on('click', function (event) {
            event.stopPropagation();
        });
        $(modalId).on('click', function () {
            $('.closeIntroVideo').trigger('click');
        });
    });

    $('.closeIntroVideo').click(function () {
        let modalId = $(this).parents('.modal');
        let iframe = $(this).parent().nextAll('.modal-body').find('iframe.iframeVideoId');
        let vidsrc = iframe.attr("src");
        iframe.attr('src', '');
        let vidSRC = vidsrc.split("?");
        iframe.attr('src', vidSRC[0]);
        $('body').find('.modal-backdrop').remove();
        $(modalId).hide();
    });

    /**
     * Toggle the checkmark on a pledge box.
     */
    $('.pledge-complete').on('click', (e) => {
        $(e.target).toggleClass('check');
    });



    /**
     * On-boarding
     */

    // Ensure that onboarding has been completed before the user can move on.
    if ($('#course-onboarding').length) {
        $('#course-navigation .next').addClass('disabled');

        $(document).on('click', (e) => {
            // All videos must be watched and the checkbox needs to be checked.
            if ($('.video-wrapper').length == $('.video-wrapper.watched').length && $('.pledge-complete').hasClass('check')) {
                $('#course-navigation .next').removeClass('disabled');
            } else {
                $('#course-navigation .next').addClass('disabled');
            }
        });
    }

    $('.disable-until-videos-complete').on('click', e => {
        if ($('.video-wrapper').length != $('.video-wrapper.watched').length) {
            e.stopImmediatePropagation();

            $('.videos-not-complete-modal').modal('show');
        }
    });

    let videoPlays = new Array($('.video-wrapper').length).fill(0),
        reveal     = true;

    for (let i = 0; i < videoPlays.length; i++) {
        showVideo(i);
 
        reveal && $('.video-wrapper').eq(i).removeClass('disabled');
        reveal = videoPlays[i];
        reveal && $('.video-wrapper').eq(i).addClass('watched');

        if (Cookies.get('onboarding_videos_' + i) == '1') {
            videoPlays[i] = 1;

            $('.video-wrapper')[i].classList.add('watched');
            
            enableNextVideo();
        }
    }
 
    function showVideo(id) {
        if (id > videoPlays.length) {
            return;
        }
 
        let video = $('#video-' + id).parent();
        let player = new Vimeo.Player('video-' + id);
        let playing = false;
        let initialPlay = true;
 
        // Set the correct height.
        video.find('iframe').css('height', video[0].offsetWidth * (9 / 16));
 
        player.on('timeupdate', function (o) {
            if (! video.hasClass('watched') && o.percent > 0.8 && ! videoPlays[id]) {
                videoPlays[id] = 1;

                video.parent().addClass('watched');

                enableNextVideo();

                Cookies.set('onboarding_videos_' + id, '1', {expires: 30});
            }
 
            video.find('.prgrs .bar').css('width', (o.percent * 100) + '%');
        });
 
        video.prepend(
            '<div class="overlay">' +
            '<div class="play">' +
            '<p>Click to Play</p>' +
            '<i class="play icon"></i>' +
            '</div>' +
            '<div class="paused">' +
            '<p>Click to Pause</p>' +
            '<i class="pause icon"></i>' +
            '</div>' +
            '</div>' +
            '<div class="prgrs"><div class="bar"></div></div>'
        );
 
        player.on('loaded', function (e) {
            if (! isMobile) {
                player.setCurrentTime(1);
                player.pause();
 
                setTimeout(function () {
                    player.pause();
                }, 1000);
            }
        });
 
        video.on('click', function () {
            if (video.parent().hasClass('disabled')) {
                $('#watch-previous-videos-modal').modal('show');
                return;
            }
 
            if (isMobile) {
                let iframe = video.find('iframe')[0];
                iframe.src = iframe.src.replace(/\?.*/, '?autoplay=1');
                video.unbind('click');
                video.find('.overlay, .preview-image').remove();
 
                return;
            }
 
            if (initialPlay) {
                player.setVolume(1);
                player.setCurrentTime(0);
                initialPlay = false;
                video.find('.preview-image').remove();
            }
 
            if (playing) {
                player.pause();
                video.removeClass('playing');
            } else {
                player.play();
                video.addClass('playing');
            }
 
            playing = ! playing;
        });
    }
 
    function enableNextVideo () {
        let video;
 
        for (let i = 0; i < videoPlays.length; i++) {
            video = $('.video-wrapper').eq(i);
 
            if (! videoPlays[i]) {
                video.removeClass('disabled');
                break;
            }
        }
 
        checkIfDone();
    }
 
    function checkIfDone () {
        for (let i = 0; i < videoPlays.length; i++) {
            if (! videoPlays[i]) {
                return false;
            }
        }
 
        $('.widget.hidden-first').first().show();
 
        return true;
    }

    $('.next-section').on('click', function () {
        let nextWidget = $(this).parents('.widget').next('.widget');
 
        nextWidget.length && nextWidget.show();
 
        $('html, body').animate({ scrollTop: nextWidget.offset().top - 30 }, 500);
    });
});

function debounce (func, wait, immediate) {
    let timeout;

    return function () {
        let context = this,
            args = arguments;

        let later = function later () {
            timeout = null;
            immediate || func.apply(context, args);
        };

        let callNow = immediate && !timeout;

        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        callNow && func.apply(context, args);
    };
}
