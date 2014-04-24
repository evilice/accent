$(function(){
    $('input[name^="modules"]').click(function(){
        var data = {
            module: $(this).val(),
            status: ($(this).is(':checked'))?1:0
        };
        
        $.post('/admin/modules/status', data);
    });
});