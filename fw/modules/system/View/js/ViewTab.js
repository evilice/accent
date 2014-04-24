$(function(){
    $('.v_tabs').each(function() {vertical_tabs_check($(this).children('.tabs_items'));});
    $('.h_tabs').each(function() {horizontal_tabs_check($(this).children('.tabs_items'));});

    $('.tabs_items').each(function() {
        $(this).children('div:not(:last)').click(function() {
            var p = $(this).parent();
            var i = $(this).index();

            p.children('.tabs_items > div').removeClass('selected');
            p.parent().children('.tabs_content').children().removeClass('selected');

            p.children('.tabs_items > div:eq('+i+')').addClass('selected');
            p.parent().children('.tabs_content').children('div:eq('+i+')').addClass('selected');

            switch(p.parent().attr('class')) {
                case 'v_tabs': { vertical_tabs_check(p); break; }
                case 'h_tabs': { horizontal_tabs_check(p); break; }
            }
        });
    });
});

function horizontal_tabs_check(s) {
    var list = s.children();
    var tbs = list.length - 2;

    var width = 0;
    for(var i=0; i<tbs; i++) width += $(list[i]).outerWidth();
    s.children('.last_item').width(s.parent().children('.tabs_content').width()-width);
}

function vertical_tabs_check(s) {
    var tb = s.height();
    var cn = s.parent().children('.tabs_content').height();
    var last = s.children('.last_item');
    
    if(cn-tb != 0) {
        if(cn-tb > 0) { last.css({height: (cn-tb)+'px'}); }
        else {
            s.children('.last_item').height(0);
            s.parent().children('.tabs_content').css('min-height', s.height()-1);
        }
    }
}