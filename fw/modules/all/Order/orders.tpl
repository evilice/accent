<div style="text-align: right;">
    <a href="/admin/content/orders/">новые</a> | <a href="/admin/content/orders/1">закрытые</a>
</div><br />
<table class="orders">
    <thead>
        <tr>
            <td class="addr">Адрес</td>
            <td>Информация</td>
            <td>&nbsp;</td>
        </tr>
    </thead>
{foreach from=$orders item=ord}
    <tbody>
        <tr>
            <td class="addr">{$ord.client}</td>
            <td>{$ord.content}</td>
            <td><a href="/admin/content/orders/confirm/{$ord.id}">x</a></td>
        </tr>
    </tbody>
{/foreach}
</table>