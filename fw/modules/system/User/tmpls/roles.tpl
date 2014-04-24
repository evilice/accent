<table class="table table-hover">
    <thead>
        <tr>
            <th style="width: 180px;">Роль</th>
            <th>Описание</th>
        </tr>
    </thead>
    <tbody>
    {foreach from=$roles item=r}
        <tr>
            <td><a href="/admin/users/role/{$r.id}">{$r.name}</a></td>
            <td>{$r.description}</td>
        </tr>
    {/foreach}
    </tbody>
</table>