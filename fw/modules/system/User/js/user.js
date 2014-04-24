User = function() {
    var s = this;

    s.checkName = function(callback) {
        $.post('/ajax/admin/users/checkName/'+$('#login').val(), {}, function(res) {
            if(callback != undefined) callback(parseInt(res));
        });
    };
    s.checkPass = function() {
        var p = $('#pass').val();
        return (p == $('#repass').val() && p.length>3);
    };
    s.sendForm = function() {
        var fl = false;
        s.checkName(function(res) {
            if(parseInt(res) == 0) {
                if(s.checkPass()) $('#user_form').submit();
                else message.add('Поле пароль заполнено неверно.', Message.TYPE_ERROR);
            } else message.add('Пользователь с таким именем уже существует.', Message.TYPE_ERROR);
        });
    };
};
user = new User();
$(function() {

    $('#btFormUA').click(function() {
        user.sendForm();
        return false;
    });
});