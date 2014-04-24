<div{{if cls}} class="${cls}"{{/if}} id="a_${code}">
    <div class="dctb_move">{{if typeof cls==="undefined"}}<div class="move"></div>{{/if}}</div>
    <div class="dctb_code">{{if cls}}${code}{{else}}<a href="#${code}" title="${code}">${code.substr((code.lastIndexOf('.')>-1)?code.lastIndexOf('.')+1:0)}</a>{{/if}}</div>
    <div class="dctb_title">${title}</div>
    <div class="dctb_val">{{if val}}${val}{{else}}&nbsp;{{/if}}</div>
    <div class="dctb_act">
        {{if typeof cls==="undefined"}}
        <div class="delete" data-act="del" title="Удалить"></div>
        <div class="edit" data-act="edit" title="Редактировать"></div>
        <div class="save" data-act="save" title="Сохранить"></div>
        {{/if}}
    </div>
    <div class="clear"></div>
</div>