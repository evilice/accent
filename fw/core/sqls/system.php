<?php
SQL::set('sys_modules_core', "select * from modules where package='core' and status=1");
SQL::set('sys_modules_other', "select * from modules where package!='core' and status=1");
SQL::set('sys_modules_on', 'select * from modules where status=1');