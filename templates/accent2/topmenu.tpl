{$dictionary->setCode('menu.admin')}
{assign var=mns value=$dictionary->childs(true)}

{foreach from=$mns item=m key=k}
{assign var=c value=isset($m.childs)}
<li{if $c} class="dropdown"{/if}>
    {if $c}
    <a href="/{str_replace('.', '/', substr($m.code, 5))}" class="dropdown-toggle" data-toggle="dropdown">{$m.title} <b class="caret"></b></a>
    <ul class="dropdown-menu">
        {foreach from=$m.childs item=p}
        <li><a href="/{str_replace('.', '/', substr($p.code, 5))}">{$p.title}</a></li>
        {/foreach}
    </ul>
    {else}
    <a href="/{str_replace('.', '/', substr($m.code, 5))}">{$m.title}</a>
    {/if}
</li>
{/foreach}