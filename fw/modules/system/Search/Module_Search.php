<?php
/**
 * Created by PhpStorm.
 * User: s.goncharov
 * Date: 25.04.14
 * Time: 15:37
 */

class Module_Search extends Module {

    private $step = 10;

    public function init() {
        incFile($this->path().'/sql.php');
    }

    public function controllers() {
        return ['search'=>['title'=>'Результаты поиска', 'callback'=>'search', 'perms'=>[]]];
    }

    public function search($filter = '', $page = 1) {
        if(!$filter) return '';
        $rate = [];
        $words = [];
        $filter = preg_replace("/[^a-zA-ZА-Яа-я0-9 _ёЁ]/u",'', strtolower($filter));

        $tw = explode(' ', $filter);
        foreach($tw as $k=>$v)
            if(mb_strlen(utf8_decode($v)) > 2) $words[] = $v;

        $str = Cache::getdb(implode(' ', $words), 'sch');
        if(!$str) { //--- Проверяем кэш фразы
            foreach($words as $w) {
                $wch = Cache::getdb($w);
                if($wch) { //--- проверяем кэш каждого слова
                    $docs = unserialize($wch['data']);
                    foreach($docs as $k=>$v) {
                        if(!isset($rate[$k])) $rate[$k] = 0;
                        $rate[$k] += $v;
                    }
                } else {
                    $rt = (new DB(SQL::fill('search', $w)))->maps();
                    $rwc = [];
                    foreach($rt as $k=>$v) {
                        $id = 'd_'.$v['id'];
                        if(!isset($rate[$id])) $rate[$id] = 0;
                        $rate[$id] += $v['cnt'];
                        $rwc[$id] = $v['cnt'];
                    }
                    arsort($rwc);
                    Cache::setdb($w, serialize($rwc), 'sch');
                }
            }

            arsort($rate);
            Cache::setdb(implode(' ', $words), serialize($rate), 'sch');
        } else $rate = unserialize($str['data']);

        $docs = (($rate)?$this->docs($rate, $page):[]);

        return $this->view(count($rate), $page, $filter, $docs);
    }

    private function docs($rate, $page) {
        var_dump($rate);
        $rate = array_chunk($rate, $this->step, true)[$page-1];
        $keys = array_keys($rate);
        array_walk($keys, function(&$item) { $item = intval(substr($item, 2)); });

        $c = new SQLConstructor();
        $c->find('docs d', ['@in'=>['d.id', $keys]]);
        $c->fields('d.id', 'd.title', 'd.alias', 'd.adt', 't.title as type');
        $c->join('doc_types', 't', ['@eqf'=> ['d.type', 't.name']], 'left');
        $docs = $c->maps('id');

        array_walk($keys, function(&$item) use ($docs, $rate) {
            $d = $docs[$item];
            $d['rate'] = $rate['d_'.$item];
            $item = $d;
        });

        return $keys;
    }

    private function view($cnt, $page, $filter, $docs) {
        $sm = new Smarty();
        $sm->setTemplateDir($this->path().'/');

        $pg = new Pagination($cnt, $this->step, 'search/'.$filter.'/', $page);
        $sm->assign('pgn', $pg->view());
        $sm->assign('docs', $docs);

        return $sm->fetch('docs.tpl');
    }
} 