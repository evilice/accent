<?php
SQL::set('dictionary_delete', "DELETE FROM dictionary WHERE code='?' or parent like '?.%' or parent='?'");
SQL::set('dictionary_up_weight', "UPDATE dictionary SET `weight`=? WHERE code='?';");
SQL::set('dictionary_with_childs', "select * from dictionary where parent='?' or parent in (SELECT code FROM `dictionary` WHERE parent='?' group by code) order by parent, weight");
SQL::set('dictionary_childs', "SELECT * FROM `dictionary` WHERE parent='?' ORDER BY weight");