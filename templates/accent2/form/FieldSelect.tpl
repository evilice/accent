<div>{if $f->upTitle()} {$f->upTitle()}<br />{/if}
<select{foreach from=$f->attrs() key=k item=v} {$k}="{$v}"{/foreach}>
{foreach from=$f->options() item=op}<option{if $op.value!==false} value="{$op.value}"{/if}{if isset($op.selected) && $op.selected != false} selected{/if}>{$op.text}</option>{/foreach}
</select>{if $f->title()} {$f->title()}{/if}</div>