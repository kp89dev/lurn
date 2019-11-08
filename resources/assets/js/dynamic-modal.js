/**
 * Dynamic Modal.
 */
$(document).ready(function () {
    $.fn.dynamicModal = function (o) {
        var defaults = {
            hovered: 'primary',
            title: 'You should use a title.',
            description: null,
            primary: 'Ok',
            secondary: 'Cancel',
            type: 'lesson'
        };

        var hovered, modalId = 'dynamic-modal-' + (Math.floor(Math.random() * 1E6));

        var modal =
            '<div id="' + modalId + '" class="dynamic-modal">\
                <div class="dialog">\
                    <div class="title"></div>\
                    <div class="description"></div>\
                    <div class="actions">\
                        <button type="button" class="ui secondary button"></button>\
                        <button type="button" class="ui primary button"></button>\
                    </div>\
                </div>\
            </div>';

        o = $.extend(defaults, o);

        $('body').append(modal);
        modal = $('#' + modalId);

        modal.on('click', 'button.primary', function (e) {
            e.stopPropagation();
            o.primaryAction && o.primaryAction(e);
            o.action && o.action(e);
        });

        modal.on('click', 'button.secondary', function (e) {
            e.stopPropagation();
            o.secondaryAction && o.secondaryAction(e);
            o.action && o.action(e);
        });

        modal.find('.title').html(o.title);
        modal.find('.dialog').addClass(o.type);

        o.description ? modal.find('.description').html(o.description) : modal.find('.description').hide();
        o.primary     ? modal.find('.primary').html(o.primary)         : modal.find('.primary').hide();
        o.secondary   ? modal.find('.secondary').html(o.secondary)     : modal.find('.secondary').hide();

        hovered = modal.find('button.' + o.hovered);

        setMaxHeight = $('body').height() - ($('body').find('#header').height()*2);
        // setTop = o.cursorPosY ? o.cursorPosY - hovered.position().top - hovered[0].offsetHeight / 1.5 : $('body').find('#header').height();
        setWidth = $('body').find('#header').find('.wrapper').width();
        // setLeft = o.cursorPosX ? o.cursorPosX - hovered.position().left - hovered[0].offsetWidth / 1.5 : $('body').find('#header').find('.wrapper').offset().left;
        
        modal.find('.dialog').css({
            maxHeight: setMaxHeight,
            top: ($(window).height() / 2) - ($('.dialog', '#' + modalId).outerHeight() / 2),
            width: setWidth,
            left: ($(window).width() / 2) - ($('.dialog', '#' + modalId).outerWidth() / 2)
        });

        $('body').on('click', '.dynamic-modal', function (e) {
            modal.hide().fadeOut(100);
        });
        
        modal.hide().fadeIn(100);
    };
});