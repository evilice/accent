{if $f->title()!=''}{$f->title()}<br />{/if}
<textarea{foreach from=$f->attrs() key=k item=v}{if $k!='value'} {$k}="{$v}"{/if}{/foreach}>{$f->value()}</textarea>