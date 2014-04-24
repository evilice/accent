<ul class="breadcrumb">
    {foreach from=$breadcrumbs item=b name=breadcrumbs}
    <li{if $smarty.foreach.breadcrumbs.last}<li class="active">{$b.title}</li>{else}<li><a href="{$b.url}">{$b.title}</a> <span class="divider">/</span></li>{/if}</li>
    {/foreach}
</ul>