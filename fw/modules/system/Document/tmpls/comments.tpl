<div class="comments" style="background: #EEE;">
    <h3>Комментарии</h3>
    <table>
    {foreach from=$comments item=com}
        <tr>
            <td><b>{$com.name}:</b></td>
            <td>{$com.body}</td>
        </tr>
    {/foreach}
    </table>
    {$form}
</div>