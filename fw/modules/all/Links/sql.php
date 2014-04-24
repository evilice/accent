<?php
SQL::set('mod_links', "SELECT d.id FROM docs d left join field_category fc on fc.doc=d.id WHERE d.type='link' and fc.val='?'");