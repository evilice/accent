<div id="breadcrumbs">
    {foreach from=$breadcrumbs item=m name=brdcrmbs}
        {if $smarty.foreach.brdcrmbs.last != true}
            <div><a href="{$m.url}" class="bc">{$m.title}</a></div>
        {else}
            <div class="bcend">{$m.title}</div>
        {/if}
    {/foreach}
</div>
<div class="clear"></div>