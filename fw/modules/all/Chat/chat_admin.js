/**
 * Created by root on 11.04.14.
 */

$(function() {
    $('body').append('<div id="inchat_admin_info"></div>')

    inchat.addListener(function() {
        $('#inchat_body > div > div').unbind('mouseover');
        $('#inchat_body > div > div').mouseover(function() {
            var el = $(this).children('div:first');
            if($(el).children('span').length == 0) {
                $(el).append('<span></span>');
                $.post('/ajax/chat/req', {cmd: 'info', mid: $(this).data('mid')}, function(res) {
                    $(el).children('span').text(' ('+res.uid + ')');
                });
            }
        });
    });
});