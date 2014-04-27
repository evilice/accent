<h3>Результаты поиска</h3>
{foreach from=$docs item=d}
    <div><a href="/{$d.alias}" title="Рейтинг - {$d.rate}">{$d.title}</a> <span>{$d.type}</span></div>
{/foreach}
{$pgn}