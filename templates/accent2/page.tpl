<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="Keywords" content="{$metaKeywords}">
        <meta name="Description" content="{$metaDescription}">

        {foreach from=$css item=j}
        <link rel="stylesheet" type="text/css" href="{$j}" />
        {/foreach}

        {foreach from=$js item=j}
        <script type="text/javascript" src="/{$j}"></script>
        {/foreach}
	</head>
	<body>
        <div class="navbar navbar-fixed-top">
            <div class="navbar-inner">
                <a class="brand" href="#" style="margin-left: 10px;">Accent <small>2.1</small></a>
                <ul class="nav">
                    <li class="divider-vertical"></li>
                    {include file="topmenu.tpl"}
                    <li class="divider-vertical"></li>
                </ul>
                <div class="pull-right">
                    <ul class="nav">
                        <li class="divider-vertical"></li>
                        <li><a href="/user/logout" title="Выход"><i class="icon-user"></i> Выход</a></li>
                    </ul>
                </div>
            </div>
            {include file="breadcrumbs.tpl"}
        </div>

        <div id="sysMessages">
            {if isset($messages.er)}<div class="sm_er">{foreach from=$messages.er item=m}<div>{$m}</div>{/foreach}</div>{/if}
            {if isset($messages.wr)}<div class="sm_wr">{foreach from=$messages.wr item=m}<div>{$m}</div>{/foreach}</div>{/if}
            {if isset($messages.inf)}<div class="sm_inf">{foreach from=$messages.inf item=m}<div>{$m}</div>{/foreach}</div>{/if}
        </div>
        {$content}
	</body>
</html>