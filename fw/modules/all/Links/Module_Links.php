<?php
/**
 * Created by JetBrains PhpStorm.
 * User: S.Goncharov
 * Date: 03.03.13
 * Time: 18:07
 * To change this template use File | Settings | File Templates.
 */
class Module_Links extends Module {
    public function init() {
        incFile($this->path().'/sql.php');
    }
    public function controllers() {
        return [
            'links'=>['title'=>'Ресурсы', 'callback'=>'page']
        ];
    }

    public function page() {
        $res = [];
        $d = new Dictionary();
        $d->setCode('type.links');
        $ctgs = $d->childs();

        foreach($ctgs as $k=>$v) {
            $d = new DB(SQL::fill('mod_links', $v['code']));
            $ids = array_keys($d->maps('id'));
            $docs = [];
            foreach($ids as $id) {
                $doc = new Document();
                $docs[] = $doc->get($id);
            }
            $res[] = [
                'title'=>$v['title'],
                'links'=>$docs
            ];
        }

        $sm = new Smarty();
        $sm->setTemplateDir($this->path().'/');
        $sm->assign('list', $res);

        return $sm->fetch('links.tpl');
    }
}
