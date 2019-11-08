jQuery(document).ready(function ($) {
    /**
     * Show the dialog with forum rules, asks user to agree
     */
    $('#courseForum').submit(function (event) {
        event.preventDefault();
        var newWindow = window.open('', '_blank');
        $.ajax({
            url: $(this).attr('action'),
            type: 'post',
            data: $('#courseForum').serialize(),
            dataType: 'json',
            success: function (_response) {
                if (_response.showRules){
                    newWindow.location.href = window.location+'/forum';
                } else {
                    if (_response.forum_link){
                        newWindow.location.href = _response.forum_link+'/entry/jsconnect?client_id=1478559935';
                    } else {
                        newWindow.close();
                        window.location = '/login';
                    }
                }
            },
            error: function (_response) {
                // Handle error
            }
        });
    });

    /**
     * Process response redirect accordingly
     */
    $('.response').submit(function (event) {
        event.preventDefault();
        var $form = $(this);
        var $link = $form.find("input[name='link']").val();
        
        $.post($form.attr('action'), $form.serialize()).always(function () {
            if ($link !== "#"){
                window.location.href = $link+'/entry/jsconnect?client_id=1478559935';
            }else{
                window.close();
            }
       });
    });
    
    /**
     * Zero Up Lab Connection
     */
    $('a[href*="zeroup-lab"]').click(function (event) {
        event.preventDefault();
        var newWindow = window.open('', '_blank');
         
        $.ajax({
            url: $('a[href*="zeroup-lab"]').attr('href'),
            type: 'post',
            data: {'tool_name':'ZeroUp Lab'},
            dataType: 'json',
            success: function (_response) {
                console.log(_response);
                newWindow.location.href = _response.link;
            },
            error: function (){
                newWindow.close();
            }
        });
    });
});
