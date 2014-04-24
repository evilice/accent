(function($){
    $.fn.view_box = function() {
        $(this).children('.view_box_title').click(function(){
            var el = this;
            var content = $(el).parent().children('.view_box_content');
            if(!$(content).is(':visible')) {
                $(content).slideDown();
                $(el).children('span').addClass('view_box_opened');
            } else {
                $(content).slideUp({complete:function(){
                        $(el).find('span').removeClass('view_box_opened');
                }});
            }
        });
    };
})(jQuery);

$(function() {
    $('.view_box').view_box();
});