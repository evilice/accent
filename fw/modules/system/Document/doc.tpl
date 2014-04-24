<!--Блок заголовка с оранжевой чертой-->
<div class="block_title">
    <h1>{$doc->fields['title']}</h1>
    <div class="design_line_orange"><hr align="left"></div>
</div>
{htmlspecialchars_decode($doc->fields['body'])}