<?php
SQL::set('doc_type_fields', "select t.*, f.module, f.undeletable from doc_type_fields t left join fields f on f.fid=t.fid where t.type='?' order by t.weight");