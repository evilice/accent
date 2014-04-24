Message = function() {
    var closeTime = 10000;
    var s = this;
    s.TYPE_ERROR = 'er';
    s.TYPE_WARN = 'wr';
    s.TYPE_INFO = 'inf';
    
    /**
     * Инициализация
     */
    s.init = function() {$('<div id="system_message_box"></div>').appendTo('body');};
    
    /**
     * Добавить новое сообщение
     */
    s.add = function(text, type) {
        type = (type != undefined)?type:s.TYPE_INFO;
        $('#system_message_box').append('<div class="sm_'+type+'">'+text+'</div>');
        var ms = $('#system_message_box > div:last-child');
        $(ms).slideDown(300).click(function(){ s.close(this); });
        setTimeout(function(){s.close(ms);}, closeTime);
    };
    
    /**
     * Закрыть сообщение
     */
    s.close = function(ms) {
        $(ms).slideUp(300, function(){$(this).remove();});
    };
};

message = new Message();
$(function(){message.init();});