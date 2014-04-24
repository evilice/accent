<div>{if $f->upTitle()}{$f->upTitle()}<br />{/if}
<input type="file"{foreach from=$f->attrs() key=k item=v} {if $k=='name'}{$k}="{$v}[]"{else}{if $k!='value'}{$k}="{$v}"{/if}{/if}{/foreach} multiple /> {$f->title()}
<div class="fieldImageSelected"></div>
</div>

{assign var=vals value=$f->value()}
{if $vals}
<div id="fieldImageValues" data-fid="{$f->name()}">
    {foreach from=$vals item=v}
    <div>
    <input name="_{$f->name()}[title_{$v.id}]" value="{$v.title}" /><br />
    {if isset($v.thumbs)}
        <img data-id="{$v.id}" src="/{end($v.thumbs)}" />
    {else}
        <img data-id="{$v.id}" src="/{$v.original}" />
    {/if}
    </div>
    {/foreach}
</div>
{/if}