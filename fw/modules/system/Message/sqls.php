<?php
SQL::set('messages_get_sid', "select * from messages where sid='?'");
SQL::set('messages_del_sid', "delete from messages where sid='?'");
SQL::set('messages_insert', "insert into messages (sid, data, dreg) values('?', '?', ?)");