{foreach from=$list item=c}
    <h4>{$c['title']}</h4>
    <p>
        {foreach from=$c['links'] item=l}
            <div><a href="{$l['field_link']}">{$l['title']}</a></div>
        {/foreach}
    </p>
{/foreach}