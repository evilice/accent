<!DOCTYPE html>
<html>
    <head>
        <title>{$title}</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
        
        <link rel="icon" href="/{$tpl_path}/favicon.ico" type="image/x-icon">
        {foreach from=$css item=j}
        <link rel="stylesheet" type="text/css" href="{$j}" />
        {/foreach}
        {foreach from=$js item=j}
        <script type="text/javascript" src="/{$j}"></script>
        {/foreach}
        {foreach from=$jstmpls item=v key=k}
        <script id="{$k}" type="text/x-jquery-tmpl">
            {$v}
        </script>
        {/foreach}
	{if isset($jsd)}<script type="text/javascript">$a = {$jsd};</script>{/if}
    </head>
    <body>
        <div id="win">
            <div id="winHeader">
                <div><div><div>
                    {include file="topMenu.tpl"}
                    <div id="helpIco" title="help"></div>
                </div></div></div>
            </div>
            <div id="winBody">
                <div class="wbCenter">
                    <div>
                        {include file="breadcrumbs.tpl"}
                        <div id="contentBox">
                            <div id="leftBox">
                                {$leftbox}
                                {include file="leftMenu.tpl"}
                            </div>
                            <div id="content">
                            	<div id="sysMessages">
                                    {if isset($messages.er)}<div class="sm_er">{foreach from=$messages.er item=m}<div>{$m}</div>{/foreach}</div>{/if}
                                    {if isset($messages.wr)}<div class="sm_wr">{foreach from=$messages.wr item=m}<div>{$m}</div>{/foreach}</div>{/if}
                                    {if isset($messages.inf)}<div class="sm_inf">{foreach from=$messages.inf item=m}<div>{$m}</div>{/foreach}</div>{/if}
                                </div>
                                {$content}
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
                <div class="clear"></div>
                <div class="wbFootter"><div><div></div></div></div>
            </div>
        </div>
    </body>
</html>
