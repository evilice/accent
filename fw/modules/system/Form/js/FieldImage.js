var fimgtpl = '<div><input type="text" name="_fieldImageNewTitle[]" placeholder="Описание" /><br /><img id="{id}" /></div>';
$(function() {
    $('#fieldImageValues > div > img').click(function() {
        var f = $(this);
        if(confirm('Вы действительно хотите удалить этот файл?')) {
            $.post('/ajax/admin/field_value/delete', {id:$(f).data('id'), fid:$(f).parent().parent().data('fid')}, function(res) {
                $(f).parent().remove();
            });
        }
    });


    $('.FieldImage').change(function() {
        var res = '';
        var box = $(this).parent().find('.fieldImageSelected');
        $(box).html('');

        var files = $(this)[0].files;
        for(var i=0; i<files.length; i++) {
            var f = files[i];
            var fr = new FileReader();
            fr.onload = function(e) {
                var cnt = $(box).children('div').length;
                var tmp = fimgtpl;
                $(tmp.replace('{id}', "fis_"+cnt)).appendTo($(box));
                document.querySelector('#fis_'+(cnt)).src = e.target.result;
            };
            fr.readAsDataURL(f);
        }
    });
});