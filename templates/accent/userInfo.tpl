<h3>Информация о пользователе</h3>
<div><b>login</b>: {$user.login}</div>
<div><b>E-mail</b>: {$user.email}</div>
<div><b>Роли</b>: <ul>{foreach from=$user.roles item=r} <li>{$r.name}</li>{/foreach}</ul></div>