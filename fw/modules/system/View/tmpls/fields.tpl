<table class="field_params_table">
    <tr>
        <td class="field_params visible_fields">
            <div class="fp_header">Поля вывода</div>
            <div class="fp_content">
                <div class="fb_values">
                </div>
                <div class="fb_add_btn_wrap">
                    <a href="#" class="fp_add_btn button autohide_ignore">Добавить</a>
                    <div class="fp_add_btn_popup autohide popup_bg">
                        {foreach from=$fields item=field}
                            <div class="btn_popup_list" data="{$field->name()}" title="Добавить">
                                <div class="bp_list_item_text">{$field->title()}</div>
                            </div>
                        {/foreach}
                    </div>
                </div>
            </div>
            <input name="fields" type="hidden" class="behavior_data" {if isset($config)}value='{$config.fields|@json_encode}'{/if}/>
        </td>

        <td class="field_params sort">
            <div class="fp_header">Сортировка</div>
            <div class="fp_content">
                <div class="fb_values">
                </div>
                <div class="fb_add_btn_wrap">
                    <a href="#" class="fp_add_btn button autohide_ignore">Добавить</a>
                    <div class="fp_add_btn_popup autohide popup_bg">
                        {foreach from=$fields item=field}
                            <div class="btn_popup_list" data="{$field->name()}">
                                <div class="bp_list_item_sorttype asc" title="По возрастанию"></div>
                                <div class="bp_list_item_sorttype desc" title="По убыванию"></div>
                                <div class="bp_list_item_text autohide_ignore">{$field->title()}</div>
                            </div>
                        {/foreach}
                        <div class="btn_popup_list" data="created">
                            <div class="bp_list_item_sorttype asc" title="По возрастанию"></div>
                            <div class="bp_list_item_sorttype desc" title="По убыванию"></div>
                            <div class="bp_list_item_text autohide_ignore">Дата создания</div>
                        </div>
                        <div class="btn_popup_list" data="weight">
                            <div class="bp_list_item_sorttype asc" title="По возрастанию"></div>
                            <div class="bp_list_item_sorttype desc" title="По убыванию"></div>
                            <div class="bp_list_item_text autohide_ignore">Вес</div>
                        </div>
                    </div>
                </div>
            </div>
            <input name="sort" type="hidden" class="behavior_data" {if isset($config)}value='{$config.sort|@json_encode}'{/if}/>
        </td>

        <td class="field_params filter">
            <div class="fp_header">Фильтр</div>
            <div class="fp_content">
                <div class="fb_values">
                </div>
                <div class="fb_add_btn_wrap">
                    <a href="#" class="fp_add_btn button autohide_ignore">Изменить</a>
                    <div class="fp_add_btn_popup_big_mask"></div>
                    <div class="fp_add_btn_popup_big popup_bg">
                        <table class="filter_table">
                            <tr class="filter_table_header">
                                <td class="ft_field">Поле</td>
                                <td>Сравнение</td>
                                <td>Значение</td>
                            </tr>
                        {foreach from=$fields item=field}
                            <tr class="filter_row" data="{$field->name()}">
                                <td>
                                    <div class="filter_field">{$field->title()}</div>
                                </td>
                                <td>
                                    <select class="filter_condition">
                                        <option value="">-- Тип сравнения --</option>
                                        {foreach from=$filters item=filter key=key}
                                            <option value="{$key}">{$filter}</option>
                                        {/foreach}
                                    </select>
                                </td>
                                <td>
                                    <input class="filter_value" value="" disabled />
                                </td>
                            </tr>
                        {/foreach}
                            <tr class="filter_row" data="stat">
                                <td>
                                    <div class="filter_field">Опубликован</div>
                                </td>
                                <td>
                                    <select class="filter_condition">
                                        <option value="">-- Тип сравнения --</option>
                                        <option value="@eqf">Равен</option>
                                    </select>
                                </td>
                                <td>
                                    <input class="filter_value" value="" disabled />
                                </td>
                            </tr>
                            <tr class="filter_row" data="top">
                                <td>
                                    <div class="filter_field">Всегда наверху</div>
                                </td>
                                <td>
                                    <select class="filter_condition">
                                        <option value="">-- Тип сравнения --</option>
                                        <option value="@eqf">Равен</option>
                                    </select>
                                </td>
                                <td>
                                    <input class="filter_value" value="" disabled />
                                </td>
                            </tr>
                        </table>
                        <div class="button ok_filters">OK</div>
                        <div class="button cancel_filters">Отмена</div>
                    </div>
                </div>
            </div>
            <input name="filter" type="hidden" class="behavior_data" {if isset($config)}value='{$config.filter|@json_encode}'{/if}/>
        </td>
    </tr>
</table>
{if isset($config)}
    <script type="text/javascript">
        behavior.fields.restore();
        behavior.sort.restore();
        behavior.filter.restore();
    </script>
{/if}