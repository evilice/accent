<?php
/**
 * Created by JetBrains PhpStorm.
 * User: goncharovsv
 * Date: 28.03.13
 * Time: 11:56
 * To change this template use File | Settings | File Templates.
 */
class Module_Ace extends Module {
    public function controllers() {
        $p = 'admin/config/ace';
        return [
            $p=>['title'=>'Редактор кода', 'callback'=>'page', 'perms'=>['ace']],
            'admin/ace'=>['callback'=>'ajax', 'perms'=>['ace']]
        ];
    }

    public function page() {
        $page = Page::getInstance();
        $page->css('/'.$this->path().'/files/module_ace.css');
        js($this->path().'/ace/ace.js');
        js($this->path().'/files/module_ace.js');

        $tpl = fileRead($this->path().'/files/tmpl.htm');

        return (new FieldButton('Открыть редактор', ['id'=>'aceOpen']))->view().$tpl;
    }

    public function ajax() {
        $p = $_POST;
        $res = null;
        switch($p['cmd']) {
            case 'tree': $res = $this->tree($p['path']); break;
            case 'read': $res = $this->open($p['file']); break;
            case 'save': $res = $this->save($p['file'], $p['content']); break;
            case 'createFile': $res = $this->createFile($p['file']); break;
            case 'createDir': $res = $this->createDir($p['dir']); break;
            case 'deleteFile': $res = $this->deleteFile($p['file']); break;
            case 'deleteDir': $res = $this->deleteDir($p['dir']); break;
            case 'rename': $res = $this->rename($p['old'], $p['new']); break;
            case 'move': $res = $this->move($p['old'], $p['new']); break;
        }
        return $res;
    }

    /**
     * Возвращает список папок и файлов
     *
     * @param $dir
     * @return Array
     */
    private function tree($path) {
        $folders = [];
        $files = [];
        $list = read_dir($path);
        if($list) {
            foreach($list as $l) {
                if(!is_dir($path.$l)) $files[] = ['n'=>$l, 't'=>0];
                else $folders[] = ['n'=>$l, 't'=>1];
            }
        }
        return json_encode(array_merge($folders, $files));
    }

    private function read($dir) {

    }

    /**
     * Возвращает текст файла
     *
     * @param $file
     * @return string
     */
    private function open($file) { return fileRead($file); }

    /**
     * Сохраняет изменения в файле
     *
     * @param $file
     * @param $text
     */
    private function save($file, $content) { fileWrite($file, $content); }

    private function createFile($file) {}
    private function createDir($dir) {}
    private function deleteDir($dir) {}
    private function deleteFile($file) {}
    private function rename($old, $new) {}
    private function move($old, $new) {}

    public function install() {
        $sql = "
            CREATE TABLE files_history(
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `uid` INT(11) NOT NULL,
                `file` VARCHAR(255) NOT NULL,
                `content` TEXT,
                `date` INT(11) NOT NULL,
                PRIMARY KEY (`id`),
                INDEX file (`file`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $db = new DB($sql);
        $this->activate();
        return ($db->execute())?true:false;
    }

    public function uninstall() {
        $this->deactivate();
        return (new DB('DROP TABLE `files_history`'))->execute();
    }

    public function activate() {
        $d = new Dictionary();
        $d->setParent('menu.admin.config');
        $d->setCode('menu.admin.config.ace');
        $d->setTitle('Ace - редактор кода');
        $d->setWeight(5);
        $d->add();
    }

    public function deactivate() {
        $d = new Dictionary();
        $d->setCode('menu.admin.config.ace');
        $d->delete();
    }

    /**
     * Права доступа
     *
     * @return array|void
     */
    public function perms() { return ['ace'=>'Редактирование кода']; }
}
