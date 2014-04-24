<table class="table{if $classes}, {$classes}{/if}">
    {if $header}
    <thead>
        <tr>
        {foreach from=$header item=hd}<th>{$hd}</th>{/foreach}
        </tr>
    </thead>
    <tbody>
    {foreach from=$data item=row}
        <tr>
        {foreach from=$row item=td}<td>{$td}</td>{/foreach}
        </tr>
    {/foreach}
    </tbody>
    {/if}
</table>