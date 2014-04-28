<h3>Результаты поиска</h3>
{foreach from=$docs item=d}
    <div><a href="{if $d.alias!=''}/{$d.alias}{else}/document/{$d.id}{/if}" title="Рейтинг - {$d.rate}">{$d.title}</a> <span>{$d.type}</span></div>
{/foreach}
{if !$docs}<div>Ничего не найдено</div>{/if}
{$pgn}