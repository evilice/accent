<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14.04.14
 * Time: 12:28
 */

class Ironnet {
    public static function uid() {
        $ip = $_SERVER['REMOTE_ADDR'];

        $db = new DB("select * from (select ag.number,INET_NTOA(s.segment) as ip from staff s, vgroups vg, agreements ag where vg.uid=ag.uid and vg.vg_id=s.vg_id) t where t.ip='".$ip."'");
        $db->openConnect('192.168.100.100', 'billing', 'sp', 'qwepoi123');
        $res = $db->map();
        if(!$res) {
            $db->query("select * from (select ag.number,INET_NTOA(ses.assigned_ip) as ip from sessionsradius ses, vgroups vg, agreements ag where vg.uid=ag.uid and vg.vg_id=ses.vg_id) t where t.ip='".$ip."'");
            $res = $db->map();
        }

        return ($res)?$res['number']:$ip;
    }
} 