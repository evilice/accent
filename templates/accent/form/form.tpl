{if $form->title()}<b>{$form->title()}</b><br />{/if}
<form{foreach from=$form->attrs() key=k item=a} {$k}="{$a}"{/foreach}>
    {foreach from=$form->listFields() item=fl}
        {if $fl->type()!='FieldGroup'}{assign var=f value=$fl}{/if}
        {assign var=tpl value=$fl->type()}
        <div>{include file="$tpl.tpl"}</div>
    {/foreach}
</form>