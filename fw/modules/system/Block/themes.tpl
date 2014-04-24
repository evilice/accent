{foreach from=$templates item=t}
    <p>
        <div><a href="{$t['url']}">{$t['name']}</a></div>
        <div><img src="/{$t['screen']}" title="{$t['description']}" style="width:100px;" /></div>
        <div>{$t['description']}</div>
    </p>
{/foreach}