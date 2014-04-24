<?php
SQL::set('user_roles', 'SELECT r.name FROM users_roles ur LEFT JOIN roles r on r.id=ur.rid WHERE ur.uid=?');
SQL::set('user_perms', "SELECT rp.permission FROM users_roles ur LEFT JOIN roles_perms rp ON rp.id=ur.rid WHERE ur.uid=? GROUP BY rp.permission");
SQL::set('role_perms', "SELECT permission FROM roles_perms WHERE id=?");