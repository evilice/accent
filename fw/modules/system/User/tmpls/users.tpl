<div class="input-append">
    <input class="input-xlarge" id="appendedInputButtons" size="16" type="text">
    <select class="input-medium"><option value="-1">Роль</option>{foreach from=$roles item=r}<option value="{$r.id}">{$r.name}</option>{/foreach}</select>
    <button class="btn" type="button" title="Поиск"><i class="icon-search"></i></button>
    <button class="btn" type="button" title="Создать"><i class="icon-plus-sign"></i></button>
</div>

<table class="table table-striped table-hover">
    <thead>
     <tr>
        <th>Имя</th>
        <th>Роль</th>
        <th>Последний визит</th>
        <th>Статус</th>
        <th>&nbsp;</th>
     </tr>
    </thead>
    <tbody>
    {foreach from=$users item=u}
    <tr>
        <td><a href="/admin/users/edit/{$u.id}">{$u.name}</a></td>
        <td>{implode(', ', $u.roles)}</td>
        <td>{if $u.lastvisit != 0}{date('H:i d.m.Y', $u.lastvisit)}{else}никогда{/if}</td>
        <td><span class="label label-{if $u.status == 1}success">Активен{else}important">Заблокирован{/if}</span></td>
        <td style="text-align: right;"><button class="btn btn-mini"><i class="icon-remove"></i></button></td>
    </tr>
    {/foreach}
    </tbody>
</table>