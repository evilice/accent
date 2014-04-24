<?php
class Module_Chat extends Module {
    public function controllers() {
        return [
            'chat'=>['title'=>'Чат', 'callback'=>'start'],
            'chat/req'=>['title'=>'', 'callback'=>'ajax'],
        ];
    }

    public function start() {
        $user = User::getInstance();
        $us = $this->getUser();

        css('/'.$this->path().'/chat.css');

        $sm = new Smarty();
        $sm->setTemplateDir($this->path().'/');
        js($this->path().'/chat.js');
        if($user->id()) js($this->path().'/chat_admin.js');

        return $sm->fetch('chat.tpl');
    }

    public function inchatForm() {
        $us = $this->getUser();
        $f = new Form('Ваше имя');
        $f->fields(new FieldText(false, ['name'=>'nickname', 'placeholder'=>$us['name']]), new FieldSubmit('Изменить'));
        $f->callback('changeName');

        return ['header'=>'', 'content'=>$f->view(), 'template'=>'chat'];
    }

    public function changeName() {
        if($_POST['nickname'] != '') {
            $us = $this->getUser();
            $name = preg_replace("/[^A-Za-z0-9_]/", "", $_POST['nickname']);

            if($name != '') {
                $_SESSION['chat_user']['name'] = $name;
                $c = new SQLConstructor();
                $c->insert('chat', [['system', '2', 0, $us['name'].' меняет имя на '.$name, time()]])
                    ->fields('name', 'usertype', 'uid', 'text', 'dt')
                    ->execute();
            } else Message::add('Недопустимое имя.', Message::MESSAGE_ERROR);
        }
    }

    public function getUser() {
        $c_user = null;
        $user = User::getInstance();

        if(isset($_SESSION['chat_user'])) {
            if($user->id()) {
                $c_user = [
                    'uid'=>$user->id(),
                    'type'=>0,
                    'name'=>$user->getName()
                ];
            } else $c_user = $_SESSION['chat_user'];
        } else if($user->id()) {
            $c_user = [
                'uid'=>$user->id(),
                'type'=>0,
                'name'=>$user->getName()
            ];
        } else {
            $c_user = [
                'uid'=>Ironnet::uid(),
                'type'=>1,
                'name'=>'guest_'.rand(1001, 9999)
            ];
        }

        $_SESSION['chat_user'] = $c_user;

        return $c_user;
    }

    public function ajax() {
        $p = $_POST;
        $c = new SQLConstructor();
        $res = [];
        $us = $this->getUser();

        switch($p['cmd']) {
            case 'load': { $res = array_reverse($c->find('chat')->limit(50)->sort(['gt'=>'dt'])->maps()); break; }
            case 'last': { $res = $c->find('chat', ['@gt'=>['dt', $p['time']]])->sort('dt')->maps(); break; }
            case 'add': {
                $c->insert('chat', [[$us['name'], $us['type'], $us['uid'], htmlspecialchars($p['text']), time()]])
                    ->fields('name', 'usertype', 'uid', 'text', 'dt')
                    ->execute();
                break; }
            case 'info': {
                if($us['type'] == 0) $res = $c->find('chat', ['id'=>intval($p['mid'])])->fields('uid')->map();
                break;
            }
        }

        header('Content-type: application/json');
        return json_encode($res);
    }

    public function install() {
        $mess = "
        CREATE TABLE chat(
            `id` INT(11) UNSIGNED AUTO_INCREMENT,
            `name` VARCHAR(32) NOT NULL,
            `usertype` INT(2) NOT NULL DEFAULT 1,
            `uid` VARCHAR(20) NOT NULL,
            `text` VARCHAR(255) NOT NULL,
            `dt` INT(11) DEFAULT 0,
            INDEX (`dt`),
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        (new DB($mess))->execute();

        $block = new Block();
        $block->add('IN_Chat - авторизация', 'Module_Chat', 'inchatForm', ['vs'=>['chat'], 'unv'=>''], '', 'ironnet', [], -100);
    }

    public function deactivate() {
        $this->uninstall();
    }

    public function uninstall() {
        (new DB("drop table `chat`"))->execute();
        return true;
    }

}