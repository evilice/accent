<div>
    <div>Показать</div>
    {$select}
</div>
<div id="box_form">
    {if isset($data)}
        <div>Тип документа</div>
        {$types}
        <div id="box_fields">
            {$data}
        </div>
    {/if}
</div>
{if not isset($data)}
    <script type="text/javascript">
        changeContent('document');
    </script>
{/if}