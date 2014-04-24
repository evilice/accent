/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
(function($){
    $.fn.tree = function(list, callback) {
        var s = this;
        var box = "placeholder";
        var d = {
            move: false,
            prev: {id: '', weight: 0},
            next: {id: '', weight: 0, left: 0, top: 0},
            c: []
        };
        var _move = function(e) {
            if(d.move) {
                var mx=20, my=10;
                $('#'+box).css({top: (e.pageY-my)+'px', left: (e.pageX-mx)+'px'});
            }
        };
        var _parse = function(ls) {
            var str = '';
            for(var i in ls) {
                var el = ls[i];
                str += '<div class="el" id="el_'+el.id+'">';
                str += '<div class="elTitle"><span>'+el.n+'</span></div>';
                if(el.c) { str += '<div class="chlds">' + _parse(el.c) + '</div>'; }
                str += '</div>';
            } return str;
        };
        
        $(document).mousemove(function(e){ _move(e); });
        
        $(this).html(_parse(new Array({id:0, c:list, n:''})));
        $('<div id="'+box+'"></div>').appendTo('body');
        $('.elTitle:gt(0) > span').css({ cursor: 'move' });
        
        $('.elTitle:gt(0) > span').mousedown(function(e){
            d.c = [];
            var pr = $(this).parent().parent();
            var ls = pr.parent().children('div');
            var w = pr.width(), h = pr.height();
            d.prev.id = (pr.parent().parent().attr('id'));
            d.prev.weight = ls.index(pr);
            
            $('#'+box).append(pr).css({
                top:(e.pageY-d.my)+'px', 
                left:(e.pageX-d.mx)+'px', 
                display: 'block',
                width: w+'px',
                height: h+'px'
            });
            d.move = true;
            _move(e);
        }).mouseup(function(e){
            var pts = $(s).find('.el:gt(0)');
            for(var cnt=pts.length, i=0; i<cnt; i++) {
                var nx = $('#'+pts[i].id);
                var p = nx.offset();
                if(e.pageY > p.top-5 && e.pageY < p.top+25){
                    d.next.id = nx.attr('id');
                    d.next.left = p.left;
                    d.next.top = p.top;
                    d.next.weight = nx.parent('.chlds').children('.el').index(nx);
                }
            }

            d.move = false;
            var el = $('#'+box).children();
            if(d.next.id == '') {
                var tm = '#'+d.prev.id+' > .chlds > div:eq([id])';
                switch(d.prev.weight) {
                    case 0:{ el.insertBefore(tm.replace('[id]', d.prev.weight)); break; }
                    case ($('#'+d.prev.id+' > .chlds > div').length + 1):{ el.insertAfter(tm.replace('[id]', d.prev.weight)); break; }
                    default: { el.insertAfter(tm.replace('[id]', d.prev.weight-1)); break; }
                }
            } else {
                if(e.pageY < d.next.top + 12) {
                    el.insertBefore('#'+d.next.id);
                } else if(e.pageX < d.next.left+40){
                    el.insertAfter('#'+d.next.id);
                } else {
                    var chlds = $('#'+d.next.id+' > .chlds');
                    if(chlds.length==0) {
                        $('#'+d.next.id).append($('<div />').addClass('chlds'));
                        chlds = $('#'+d.next.id+' > .chlds');
                    }
                    $(el).appendTo(chlds);
                }
                d.next.weight = el.parent('.chlds').children('.el').index(el);

                callback({
                    id: (el.attr('id')).split('_')[1],
                    pid: (el.parents('.el').eq(0).attr('id').split('_')[1]),
                    weight: el.parent('.chlds').children('.el').index(el)
                });
            }
            $('#'+box).css({display: 'none'});
        });
    };
})(jQuery);