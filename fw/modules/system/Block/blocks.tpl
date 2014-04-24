{foreach from=$places item=p}
    {if $p['blocks']}
    <fieldset>
        <legend>{$p['title']}</legend>
        {if $p['blocks']}
            {foreach from=$p['blocks'] item=b}
            <div><a href="/{$url}/{$b['id']}">{$b['title']}</a></div>
            {/foreach}
        {/if}
    </fieldset>
    {/if}
{/foreach}
{if $blocks}
<hr />
<fieldset>
    <legend>Не установленные</legend>
    {foreach from=$blocks item=block}
        <div><a href="/{$url}/{$block['id']}">{$block['title']}</a></div>
    {/foreach}
</fieldset>
{/if}