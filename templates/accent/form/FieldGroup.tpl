{if $fl->listFields()}
{if $fl->title()}<b>{$fl->title()}</b>{/if}<br />
<div class="acFieldsGroup{if $fl->title()} acFGBorder{/if}">
    {foreach from=$fl->listFields() item=f}
        {assign var=tp value=$f->type()}
        <div>{include file="$tp.tpl"}</div>
    {/foreach}
</div>
<div class="clear"></div>
{/if}