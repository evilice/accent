<div>{if $f->upTitle()}{$f->upTitle()}<br />{/if}
<input type="file"{foreach from=$f->attrs() key=k item=v} {if $k=='name'}{$k}="{$v}[]"{else}{$k}="{$v}"{/if}{/foreach} multiple /> {$f->title()}</div>
{assign var=vals value=$f->value()}
{if $vals}
<div id="fieldFileValues" data-fid="{$f->name()}">
    {foreach from=$vals item=v}
        <div data-id="{$v.id}" title="Удалить '{$v.title}'">{$v.title}</div>
    {/foreach}
</div>
{/if}