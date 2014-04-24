$(function() {
    $('#fieldFileValues > div').click(function() {
        var f = $(this);
        if(confirm('Вы действительно хотите удалить этот файл?')) {
            $.post('/ajax/admin/field_value/delete', {id:$(f).data('id'), fid:$(f).parent().data('fid')}, function(res) {
                $(f).remove();
            });
        }
        return false;
    });
});