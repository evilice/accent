<div class="field_item bold">
    <div class="field_fid">ID</div>
    <div class="field_module">Модуль</div>
    <div class="field_title">Название</div>
    <div class="field_description">Описание</div>
</div>
<ul class="sortable_list">
    {foreach from=$fields item=field}
    <li class="sortable_list_item">
        <div class="field_item">
            <div class="field_fid">{$field.fid}</div>
            <div class="field_module">{$field.module}</div>
            <div class="field_title">{$field.title}</div>
            <div class="field_description">{$field.description}</div>
            <input type="hidden" class="field_type" value="{$field.type}"/>
        </div>
    </li>
    {/foreach}
</ul>