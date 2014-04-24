<div class="{$type}_tabs">
    <div class="tabs_items">{foreach from=$tabs item=tl name=tabs_tts}<div{if $smarty.foreach.tabs_tts.index == 0} class="selected"{/if}>{$tl}</div>{/foreach}<div class="last_item">&nbsp;</div>{if $type=='h'}<div class="clear"></div>{/if}</div>
    <div class="tabs_content">{foreach from=$contents item=c name=tabs_cnt}<div{if $smarty.foreach.tabs_cnt.index == 0} class="selected"{/if}>{$c}</div>{/foreach}&nbsp;</div>
    <div class="clear"></div>
</div>