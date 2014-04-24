<div>{if $f->upTitle()}{$f->upTitle()}<br />{/if}
<input type="text"{foreach from=$f->attrs() key=k item=v} {$k}="{$v}"{/foreach} /> {$f->title()}
{if $f->description()}<div><i>{$f->description()}</i></div>{/if}
</div>