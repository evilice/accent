{if $f->title()!=''}{$f->title()}<br />{/if}
<textarea{foreach from=$f->attrs() key=k item=v} {$k}="{$v}"{/foreach}>{$f->value()}</textarea>