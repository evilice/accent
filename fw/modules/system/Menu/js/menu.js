CMenu = function() {
    var s = this;
    var url = '/admin/structure/menu/ajax/';
    changeList = {};
    
    s.init = function() {
        $('<div />').addClass('boxMenu').appendTo('#content');
        $.post(url+'list', null, function(res){
            $('.boxMenu').tree(eval(res), function(el){
                changeList['el'+el.id] = el;
            });
            $('<div><a href="#">edit</a></div>').appendTo('.elTitle:gt(0)');
            $('.elTitle > div > a').click(function(){
                var id = ($(this).parents('.el').attr('id')).split('_')[1];
                g2p('/admin/structure/menu/edit/'+id);
            });
        });
        $('#btMenuSave').click(s.save);
        $('#btMenuCreate').click(function(){ g2p('/admin/structure/menu/add'); });
    };

    s.save = function() {
        $.post(url+'save', {changeList: changeList}, function(){});
        return false;
    };
}

var cm = new CMenu();
$(function() { cm.init(); });