<div class="{if isset($table['class'])}{$table['class']} {/if}tvTable">
{foreach from=$data item=dts}
    {assign var=i value="r_{$dts@index}"}
    <div{if isset($rows[$i]['class'])} class="{$rows[$i]['class']}"{/if}>
        {foreach from=$dts item=row}
            {assign var=style value=''}
            {assign var=class value=''}
            
            {* Зполнение классов *}
            {if isset($cells["{$row@index}_n"]['class'])}{$class = "{$class}{$cells["{$row@index}_n"]['class']}"}{/if}
            {if isset($cells["{$row@index}_{$dts@index}"]['class'])}{$class = "{$class} {$cells["{$row@index}_{$dts@index}"]['class']}"}{/if}
            {if isset($cells["n_{$dts@index}"]['class'])}{$class = "{$class}{$cells["n_{$dts@index}"]['class']}"}{/if}
            
            {* Зполнение стилей *}
            {if isset($cells["{$row@index}_n"]['style'])}{$style = "{$style}{$cells["{$row@index}_n"]['style']}"}{/if}
            {if isset($cells["{$row@index}_{$dts@index}"]['style'])}{$style = "{$style}{$cells["{$row@index}_{$dts@index}"]['style']}"}{/if}
            {if isset($cells["n_{$dts@index}"]['style'])}{$style = "{$style}{$cells["n_{$dts@index}"]['style']}"}{/if}
            
            <div{if $class!=''} class="{$class}"{/if}{if $style!=''} style="{$style}"{/if}>{if $row!=''}{$row}{else}&nbsp;{/if}</div>
        {/foreach}
    </div>
    <div class="clear"></div>
{/foreach}
</div>
<div class="clear"></div>