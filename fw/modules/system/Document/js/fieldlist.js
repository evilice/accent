var url = '/ajax/admin/structure/fields/';
$(function(){
    $('.sortable_list').sortable({
        placeholder: "sortable_list_item placeholder",
        update:function(){
            var data = [];
            $('.sortable_list').find('.sortable_list_item').each(function(){
                data.push({
                    'fid':$(this).find('.field_fid').text(),
                    'type':$(this).find('.field_type').val(),
                    'weight':$(this).index()
                });
            });
            $.post(url+'weight', {data: data}, function(resp) {
                if(resp == ''){
                    message.add('Данные успешно сохранены.');
                }
            });
        }
    });
});