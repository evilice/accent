<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.google.com/schemas/sitemap/0.90">
    {foreach from=$docs item=d}
    <url>
        <loc>http://{$host}/{if $d.alias != ''}{$d.alias}{else}document/{$d.id}{/if}</loc>
        <lastmod>{date('c', $d.created)}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.5</priority>
    </url>
    {/foreach}
</urlset>