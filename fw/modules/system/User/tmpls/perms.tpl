<table class="table table-hover">
    <thead>
    <tr>
        <th>Параметры доступа</th>
        {foreach from=$roles item=r}
        <th>{$r.name}</th>
        {/foreach}
    </tr>
    </thead>
    <tbody>
    {foreach from=$perms key=module item=prms}
        {foreach from=$prms key=prm item=rls}
        <tr>
            <td><i><b>({$module})</i></b> {$rls.name}</td>
            {foreach from=$roles item=r}
            <td><input type="checkbox" value="{$prm}" name="role_{$r.id}[]"{if in_array($r.id, $rls.roles)} checked{/if} /></td>
            {/foreach}
        </tr>
        {/foreach}
    {/foreach}
    </tbody>
</table>