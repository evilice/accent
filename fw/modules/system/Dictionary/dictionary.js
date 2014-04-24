Dictionary = function() {
    var s = this;
    var parent = '';
    var url = '/admin/structure/dictionary/ajax/';
    var list;
    var box;
    var history = {list:{}, hash: {}};
    var move = {
        go: false,
        lastId: -1,
        obj: null,
        target: null,
        coords: {max:0, min:0}
    };
    
    /**
     * Инициализация
     */
    s.init = function() {
        window.location = '#';
        $('#bt_add_new').click(function() {
            s.add(); 
            return false; 
        });
        window.addEventListener("hashchange", s.g2, false);
        box = $('#dc_content > div.dctb');
        s.g2();
        $(window).mousemove(function(e) { s.moving(e); });
    };
    
    /**
     * Переход на другую страницу
     */
    s.g2 = function() {
        parent = window.location.hash.substr(1);
        var key = (parent == '')?'_':parent;
        if(history.list[key] != undefined) {
            list = history.list[key];
            s.show();
        } else {
            $.post(url+'childs', {parent: parent}, function(res) {
                list = eval('('+res+')');
                history.list[key] = list;
                for(var i=0; i<list.length; i++)
                    history.hash[list[i].code] = list[i];
                s.show();
            });
        }
    };
    
    /**
     * Вывод даннх
     */
    s.show = function() {
        /******************************
         *  Вывод терминов справочника
         ******************************/
        var rows = [];
        $(box).children().remove();
        rows.push({code: 'Код', title:'Название', val:'Значение', cls:'dctb_header'});
        rows = rows.concat(list);
        $('#dc_table_row').tmpl(rows).appendTo(box);
        $('.dctb_act > div').click(function(){ s.action(this); });
        
        /*********************
         * Вывод breadcrumbs
         *********************/
        var ls = parent.split('.');
        var trace = [];
        var breadcrumbs = [];
        for(var i=0; i<ls.length; i++) {
            trace.push(ls[i]);
            var el = history.hash[trace.join('.')];
            if(el) breadcrumbs.push({
                link:el.code,
                title:el.title,
                last:(i==ls.length-1)?true:false
            });
        }
        if(breadcrumbs.length > 0) breadcrumbs.splice(0,0, {title:'#', link:'', last:!(breadcrumbs.length>0)});
        $('#dc_breadcrumbs_box').html($('#dc_breadcrumbs').tmpl(breadcrumbs));
        s.initMoving('.dctb_move');
    };
    
    s.initMoving = function(el) {
        $(el).mousedown(function(e){ s.startMoving(e, this); });
        $('.dctb > div:gt(0)').mouseup(function() { move.target = this; });
        $(window).mouseup(function(e){ s.endMoving(e); });
    };
    s.startMoving = function(e, obj) {
        move.obj = $(obj).parent();
        move.lastId = $(move.obj).index();
        var p = move.obj.position();
        
        $('#dc_drop_box > div').append($(move.obj));
        var pr = $('.dctb');
        var ps = pr.position();
        move.coords.min = ps.top + $('.dctb_header').height() - 10;
        move.coords.max = ps.top+pr.height();
        
        $('#dc_drop_box').css({
            display: 'block',
            top: p.top+'px',
            left: p.left+'px',
            opacity: '0.6'
        });
        move.go = true;
    };
    s.moving = function(e){
        var y = e.pageY+10;
        if(move.go && y<move.coords.max && y>move.coords.min) $('#dc_drop_box').css({ top: (y)+'px' });
    };
    s.endMoving = function(e) {
        if(move.obj!=null && move.target) {
            /**
             *  Если объект, относительно которого будет происходить изменение веса
             *  выбранного объекта, указан то ...
             */
            
            //--- Определяем куда будем вставлять объект
            var p = $(move.target).position();
            if(e.pageY > p.top+$(move.target).height()/2) {
                $(move.obj).insertAfter(move.target);
            } else $(move.obj).insertBefore(move.target);

            //--- Переписываем историю
            var weight = {};
            var res = [];
            var key = (parent == '')?'_':parent;
            var list = history.list[key];
            var box = $('.dctb');
            
            for(var i=0; i<list.length; i++) {
                var el = list[i];
                var index = $('#a_'+el.code.replaceAll('.', '\\.')).index()-1;
                el.weight = index;
                res[index] = el;
                weight[el.code] = index;
            }
            history.list[key] = res;
            s.saveWeight(weight);
        } else {
            //--- Если цель не найдена, возвращаем объект на место
            $(move.obj).insertAfter($('.dctb > div:eq('+(move.lastId-1)+')'));
        }
        
        //--- Сбрасываем данные об операции
        $('#dc_drop_box').css({display: 'none'});
        move = {
            go: false,
            lastId: -1,
            obj: null,
            target: null,
            coords: {max:0, min:0}
        };
    };

    /**
     * Контроллер действия
     *
     * @param el
     */
    s.action = function(el) {
        var id = $(el).parent().parent().attr('id').substr(2);
        switch($(el).data('act')) {
            case 'del': { s.del(id); break; }
            case 'edit': { s.edit(id); break; }
            case 'save': { s.save(id); break; }
        }
    };
    
    /**
     * Добавление нового узла
     */
    s.add = function() {
        if($('#new_code').val().length == 0){
            message.add('Поле "Код" обязательно для заполнения', message.TYPE_ERROR);
            return;
        }
        else if(!/^[\w\d]+$/.test($('#new_code').val())){
            message.add('Неправильный формат поля "Код"', message.TYPE_ERROR);
            return;
        }
        var data = {
            code: ((parent!='')?parent+'.':'')+$('#new_code').val(),
            title: $('#new_title').val(),
            val: $('#new_value').val(),
            parent: parent,
            weight: ($(box).children().length-1)
        };
        $.post(url+'add', data, function() {
            //--- Добавляем элемент в историю
            history.hash[data.code] = data;
            
            //--- Отчищаем форму
            $('#form_add_dictionary').find('input, textarea').val('');
            
            //--- Добавляем элемент в html
            $('#dc_table_row').tmpl([data]).appendTo(box);
            var el = '#a_'+(data.code.split('.').join('\\.'));
            $(el+' .dctb_act > div').click(function(){s.action(this);});
            s.initMoving(el+' .dctb_move');
            message.add('Данные успешно добавлены.');
        });
    };

    /**
     * Изменение веса элемента
     *
     * @param data
     */
    s.saveWeight = function(data) {
        $.post(url+'weight', {data: data}, function() {
            message.add('Данные успешно сохранены.');
        });
    }
    
    /**
     * Удаление узла
     */
    s.del = function(id) {
        id = id.split('.').join('\\.');
        $.post(url+'/del', {code: id}, function(res){
            if(res == 'ok') {
                //--- Удаляем элемент из html
                $('#a_'+id).remove();
                
                //--- Удаляем элемент из истории
                delete history.list[id];
                delete history.hash[id];
                
                //--- Удаляем данные об элементе из родителя
                var pr = id.substr(0, id.lastIndexOf('.'));
                    pr = (pr=='')?'_':pr;
                for(var i=0; i<history.list[pr].length; i++)
                    if(history.list[pr][i]['code'] == id)
                        delete history.list[pr][i];
                
                //--- Удаляем потомков из истории
                var re = new RegExp("^"+(id+'.').split('.').join('\.'));
                for(var key in history.list) {
                    if(re.test(key)) {
                        delete history.list[key];
                        delete history.hash[key];
                    }
                }
                message.add('Данные успешно удалены.');
            }
        });
    };
    
    /**
     * @todo збыл что хотел доделать ))
     */
    s.edit = function(id) {
        var qid = $('#a_'+id.replaceAll('.', '\\.'));
        var o = history.hash[id];
        qid.find('.dctb_title').html('<input type="text" value="'+o.title+'" />');
        qid.find('.dctb_val').html('<textarea>'+o.val+'</textarea>');
        qid.find('.dctb_act > .save').css('display', 'block');
    };
    
    s.save = function(id) {
        var qid = $('#a_'+id.replaceAll('.', '\\.'));
        var title = qid.find('input').val();
        var text = qid.find('textarea').val();
        
        $.post(url+'/save', {code: id, title: title, val: text}, function(res){
            if(res == '1') {
                history.hash[id].title = title;
                history.hash[id].val = text;
            }
            qid.find('.dctb_act > .save').css('display', 'none');
            qid.find('.dctb_title').text(history.hash[id].title);
            qid.find('.dctb_val').text(history.hash[id].val);
        });
    };
};

dc = new Dictionary();
$(function(){ dc.init(); });