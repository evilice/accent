<div id="mainMenu">
    {foreach from=$mainMenu item=m}
        {if isset($m.childs)}
            <div>
                <a class="mn" href="{$m.url}">{$m.title}</a>
                <div class="smenu">
                    <div class="smenuCont">
                        <div>
                            {foreach from=$m.childs item=c}
                                <div><a class="mn" href="{$c.url}">{$c.title}</a></div>
                            {/foreach}
                        </div>
                    </div>
                    <div class="smenuBt"><div><div></div></div></div>
                </div>
            </div>
        {else}
            <div><a class="mn" href="{$m.url}">{$m.title}</a></div>
        {/if}
    {/foreach}
</div>
<div class="clear"></div>