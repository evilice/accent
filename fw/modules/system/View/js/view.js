tpl = {
    replace: function(data, t){
        t = this[t];
        for(var d in data) t = t.replaceAll('['+d+']', data[d]);
        return t;
    },
    
    text: '<div><input type="text" name="[name]" />&nbsp;[title]</div>',
    group: '<p><fieldset><legend>[title]</legend>[content]</fieldset></p>',
    chbs: '<div><label><input type="checkbox" name="[name]" value="[value]" />[title]</label></div>',
    rbs: '<div><label><input type="radio" name="[name]" value="[value]" />[title]</label></div>',
    select: '<div>[title]</div><select name="[id]" id="[id]">[options]</select>',
    mselect: '<select name="[id][]" id="[id]" multiple>[options]</select>',
    option: '<option value="[value]">[text]</option>'
}

//--- Форма работы с документами
Doc = function(){
    var url = '/ajax/request/admin/structure/views/';
    var s = this;
    s.init = function() {
        $.post(url+'types', null, function(res){
            s.types(eval(res));
        });
    };

    s.form = function(res) {
        $('#box_form').html(res);
    };

    /**
     * Список типов документов
     */
    s.types = function(list) {
        var str = tpl.replace({value:'',text:''}, 'option');
        for(var i=0; i<list.length; i++) {
            var el = list[i];
            str += tpl.replace({value: el.name, text: el.title}, 'option');
        }
        $('#box_form').html(tpl.replace({id: 'docType', title:'Тип документа', options: str}, 'select'));
        $('#box_form').append('<div id="box_fields"></div>');
        $('#docType').change(function() {
            var tp = $(this).val();
            $.post(url+'fields', {type:tp}, function(r){
                //s.fields(eval(r))
                $('#box_fields').html(r);
            });
        });
    };
    /**
     * Список доступных полей
     */
    s.fields = function(list) {
        var out='', srt='', filter = '';
        for(var i=0; i<list.length; i++) {
            var el = list[i];
            out += tpl.replace({name: 'fields[]', value:el.fid, title:el.title}, 'chbs');
            srt += tpl.replace({name: 'sort', value:el.fid, title:el.title}, 'rbs');
            filter += tpl.replace({name: 'filter', value:el.fid, title:el.title}, 'rbs');
        }
        srt += tpl.replace({name: 'sort', value:'created', title:'Дата создания'}, 'rbs');
        srt += tpl.replace({name: 'sort', value:'weight', title:'Вес'}, 'rbs');
        $('#box_fields').html(
            tpl.replace({title:'Поля вывода', content:out}, 'group') +
            tpl.replace({title:'Сортировать по', content:srt}, 'group') +
            tpl.replace({title:'Фильтр', content:filter}, 'group') +
            tpl.replace({title:'последних записей', name:'count'}, 'text') +
            tpl.replace({title:'строк на странице', name:'rows'}, 'text')
        );
    };
};

DC = function(){
    var s = this;
    s.init = function(){
        $('#box_form').html(tpl.replace({name: 'dcode', title:'Код словаря'}, 'text'));
    };
};
DB = function(){ var s = this; s.init = function(){};};

changeContent = function() {
    var id = $('#flContent').val();
    $('#box_tabs > div').css('display', 'none');
    $('#tab_'+id).css('display', 'block');
    var o = {init:function(){}};
    switch(id) {
        case 'document': { o = new Doc(); break; }
        case 'dictionary': { o = new DC(); break; }
        case 'db': { o = new DB(); break; }
    }
    o.init();
}

var behavior = {
    fields: {
        add: function(item){
            item.attr({
                'title': 'Удалить'
            });
            item.parents('.fp_content').find('.fb_values').append(item);
            item.parents('.fp_add_btn_popup').hide();
        },
        update: function(){
            var data = [];
            $('.visible_fields .fb_values .btn_popup_list').each(function(){
                data.push($(this).attr('data'));
            });
            $('input[name="fields"]').val(JSON.stringify(data));
        },
        remove: function(item){
            item.attr('title', 'Добавить');
            item.parents('.fp_content').find('.fp_add_btn_popup').append(item);
        },
        restore: function(){
            var data = eval($('input[name="fields"]').val());
            var item;
            for(var i in data){
                item = $('.visible_fields .btn_popup_list[data="'+data[i]+'"]');
                behavior.fields.add(item);
            }
        }
    },
    sort: {
        add: function(item){
            item.parent().find('.bp_list_item_sorttype').removeClass('chosen');
            item.addClass('chosen');
            var sorttype = item.hasClass('asc') ? 'asc' : 'desc';
            var $elem = item.parents('.btn_popup_list');
            $elem.attr({
                'title' : 'Удалить',
                'data' : '(' + JSON.stringify({
                    'fid' : $elem.attr('data'),
                    'sorttype' : sorttype
                }) + ')'
            });
            item.parents('.fp_content').find('.fb_values').append($elem);
            item.parents('.fp_add_btn_popup').hide();
        },
        update: function(){
            var data = [];
            $('.sort .fb_values .btn_popup_list').each(function(){
                data.push(eval($(this).attr('data')));
            });
            $('input[name="sort"]').val(JSON.stringify(data));
        },
        remove: function(item){
            var data = eval(item.attr('data'));
            item.attr('title', 'Добавить');
            item.attr('data', data['fid']);
            item.parents('.fp_content').find('.fp_add_btn_popup').append(item);
        },
        restore: function(){
            var data = eval($('input[name="sort"]').val());
            var item;
            for(var i in data){
                item = $('.sort .btn_popup_list[data="'+data[i]['fid']+'"]').find('.bp_list_item_sorttype.' + data[i]['sorttype']);
                behavior.sort.add(item);
            }
        }
    },
    filter: {
        add: function(){
            var $elem = '';
            var txt = '';
            $('.fp_add_btn_popup_big, .fp_add_btn_popup_big_mask').hide();
            $('.field_params.filter .fp_content').find('.fb_values').html('');
            $('.filter_row').each(function(i){
                if($(this).find('.filter_condition').val().length > 0){
                    txt = $(this).find('.filter_field').text() + ': ';
                    txt += $(this).find('.filter_condition').find('option:selected').text() + ' \'';
                    txt += $(this).find('.filter_value').val().trim().length > 0
                        ? $(this).find('.filter_value').val()
                        : (i + 1) + '-й параметр из URL';
                    txt += '\'';

                    $elem = $('<div>').attr({
                        'class':'btn_popup_list',
                        'title':'Удалить',
                        'data': '(' + JSON.stringify({
                            'fid':$(this).attr('data'),
                            'condition':$(this).find('.filter_condition').val(),
                            'value':$(this).find('.filter_value').val().replaceAll('*', '%')
                        }) + ')'
                    }).append($('<div>').attr({
                        'class':'bp_list_item_text'
                    }).text(txt));

                    $(this).parents('.fp_content').find('.fb_values').append($elem);
                }
            });
        },
        update: function(){
            var data = [];
            $('.filter .fb_values .btn_popup_list').each(function(){
                data.push(eval($(this).attr('data')));
            });
            $('.filter_row').each(function(){
                $(this).find('.filter_value')
                    .attr('disabled', 'disabled')
                    .val('');
                $(this).find('.filter_condition option:eq(0)').attr('selected', 'selected');
            });
            for(var i in data){
                filter = $('.filter_row[data="'+data[i]['fid']+'"]');
                filter.find('.filter_condition option[value="'+data[i]['condition']+'"]').attr('selected', 'selected');
                filter.find('.filter_value').removeAttr('disabled').val(data[i]['value'].replaceAll('%', '*'));
            }
            $('input[name="filter"]').val(JSON.stringify(data));
        },
        remove: function(item){
            item.remove();
        },
        restore: function(){
            var data = eval($('input[name="filter"]').val());
            $('.filter_row').each(function(){
                $(this).find('.filter_value')
                    .attr('disabled', 'disabled')
                    .val('');
                $(this).find('.filter_condition option:eq(0)').attr('selected', 'selected');
            });
            for(var i in data){
                filter = $('.filter_row[data="'+data[i]['fid']+'"]');
                filter.find('.filter_condition option[value="'+data[i]['condition']+'"]').attr('selected', 'selected');
                filter.find('.filter_value').removeAttr('disabled').val(data[i]['value'].replaceAll('%', '*'));
            }
            behavior.filter.add();
        },
        set_condition: function(item){
            if(item.val().length > 0){
                item.parents('.filter_row').find('.filter_value').removeAttr('disabled');
            }
            else{
                item.parents('.filter_row')
                    .find('.filter_value')
                    .attr('disabled', 'disabled')
                    .val('');
            }
        },
        close_popup: function(){
            $('.fp_add_btn_popup_big, .fp_add_btn_popup_big_mask').hide();
        }
    },
    all: {
        show_popups: function(item){
            var popup = item.parent().find('.fp_add_btn_popup, .fp_add_btn_popup_big');
            if(popup.find('.btn_popup_list, .filter_row').length > 0){
                $('.fp_add_btn_popup').hide();
                popup.fadeIn(100);
                if(item.parents('.filter').length > 0){
                    $('.fp_add_btn_popup_big_mask').css({
                        'width':$(window).width() + 'px',
                        'height':$(window).height() + 'px'
                    }).show();
                }
            }
        },
        close_popups: function(item){
            if(!item.is('.autohide, .autohide_ignore')){
                $('.autohide:visible').hide();
            }
        }
    }
}

$(function(){
    $('#pgCreate').click(function(){
        var els = $('#pgMenu, #pgCache, #pgAlias');
        if($(this).is(':checked')) els.removeAttr('disabled');
            else els.attr('disabled', 'disabled');
    });
    $('#blCreate').change(function(){
        var els = $('#blCache, #blUrls, #blNUrls');
        if($(this).is(':checked')) els.removeAttr('disabled');
            else els.attr('disabled', 'disabled');
    });
    $('#pgMenu').change(function(){
        if($(this).is(':checked')){
            $.post(
                '/ajax/admin/structure/dictionary/ajax/childs',
                {'parent':'menu'},
                function(resp){
                    resp = eval(resp);
                    $('#menu_selected_tpl').tmpl(resp).appendTo('#pgMenuSelected');
                    $('#pgMenuSelected').removeAttr('disabled');
                }
            );
        }
        else{
            $('#pgMenuSelected').attr('disabled', 'disabled').html('');
        }
    });
    
    //$('#pgMenu, #pgCache, #pgAlias, #blCache, #blUrls, #pgMenuSelected').attr('disabled', 'disabled');
    
    $('#tab_document').css('display', 'block');
    $('#flContent').change(function(){ changeContent(); });
    
    // Скрытие всплывающих окон при клике на свободном месте
    $(document).click(function(e){
        behavior.all.close_popups($(e.target));
    })
    
    // Отображение всплывающих окон
    $('.fp_add_btn').live('click', function(e){
        e.preventDefault();
        behavior.all.show_popups($(this));
    });
    
    // Добавление отображаемых полей
    $('.visible_fields .fp_add_btn_popup .btn_popup_list').live('click', function(){
        behavior.fields.add($(this));
        behavior.fields.update();
    });
    
    // Добавление сортировки
    $('.fp_add_btn_popup .bp_list_item_sorttype').live('click', function(){
        behavior.sort.add($(this));
        behavior.sort.update();
    });
    
    // Добавление фильтров
    $('.ok_filters').live('click', function(){
        behavior.filter.add();
        behavior.filter.update();
    });
    
    // Удаление выбранных полей/сортировки/фильтров
    $('.fb_values .btn_popup_list').live('click', function(){
        if($(this).parents('.filter').length > 0){
            behavior.filter.remove($(this));
            behavior.filter.update();
        }
        else if($(this).parents('.sort').length > 0){
            behavior.sort.remove($(this));
            behavior.sort.update();
        }
        else{
            behavior.fields.remove($(this));
            behavior.fields.update();
        }
    });
    
    // Выбор условия фильтра
    $('.filter_condition').live('change', function(){
        behavior.filter.set_condition($(this));
    });
    
    // Закрытие окна с фильтрами
    $('.cancel_filters').live('click', function(){
        behavior.filter.close_popup();
    });
});