$(document).ready(function(){
    $('.trackable').on('click', function() {
        var $event = 'Clicked ' + $(this).attr('data-event-name');
        var extra = {'email': window.email, 'name': window.name };

        woopra.track($event, extra);
    });
});
