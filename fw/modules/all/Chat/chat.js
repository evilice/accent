INChat = function() {
    var pause = 5000; //--- ms

    var update = 0;
    var timer = null;
    var listener = null;
    var tpl = '<div data-mid="{id}"><div class="inchat_name inchat_{type}">{name}</div><div class="inchat_text">{text}</div></div>';
    var url = '/ajax/chat/req';

    //--- Запуск клиентской части чата (загрузка последних сообщений)
    var start = function() { $.post(url, {'cmd': 'load'}, view); };

    //--- Периодическая загрузка данных
    var load = function() { $.post(url, {'cmd': 'last', 'time': update}, view); };

    //--- Показ новых сообщений
    var view = function(res) {
        var str = '';
        if(timer) clearTimeout(timer);
        if(res.length > 0) update = res[res.length-1]['dt'];

        if(res.length > 0) {
            $('.inchat_name').unbind('click');

            for(var i in res) { str += render(res[i]); }
            $('#inchat_body > div').append(str);
            $("#inchat_body").scrollTop($("#inchat_body > div").height());
            if(listener) listener();

            $('.inchat_name').click(function() {
                $('#inchat_stext').val($('#inchat_stext').val() + '»' + $(this).text());
            });
        }

        timer = setTimeout(load, pause);
    };

    //--- Генерация html-сообщения
    var render = function(mes) {
        var str = tpl.replace('{id}', mes['id']);
            str = str.replace('{name}', mes['name']);
            str = str.replace('{text}', mes['text']);
            str = str.replace('{type}', mes['usertype']);
        return str;
    };

    //--- Добавление нового сообщения
    var add = function() {
        var str = $('#inchat_stext').val();
        if(str != '') {
            $.post(url, {cmd: 'add', text: str}, load);
            $('#inchat_stext').val('');
        }
    };

    this.addListener = function(l) { listener = l; };

    //--- Инициализация и запуск
    $(function() {
        start();
        $('#inchat_send').click(add);
        $('#inchat_stext').keydown(function (e) {
            if (e.ctrlKey && e.keyCode == 13) add();
        });

        var maxlength = function() {
            var t = $('#inchat_stext').val();
            if(t.length >= 250) {
                $('#inchat_stext').val(t.substr(0, 249));
            }
            $('#chat_sumb_input').text(t.length);
        };
        $('#inchat_stext').keypress(maxlength);
        $('#inchat_stext').keyup(maxlength);

    });
};

inchat = new INChat();