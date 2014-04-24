<?php
/**
 * Created by JetBrains PhpStorm.
 * User: S.Goncharov
 * Date: 03.03.13
 * Time: 18:07
 * To change this template use File | Settings | File Templates.
 */
class Module_Order extends Module {
    public function init() {
        incFile($this->path().'/sql.php');
    }
    public function controllers() {
        return [
            'admin/content/orders'=>['title'=>'Заявки клиентов', 'callback'=>'orders', 'perms'=>['order mng']],
            'admin/content/orders/confirm'=>['title'=>'Обработка заявки', 'callback'=>'confirm', 'perms'=>['order mng']]
        ];
    }

    public function orders($filter = false) {
        css('/'.$this->path().'/order.css');

        $filter = (!$filter)?['uid'=>0]:['@noteq'=>['uid', 0]];
        $c = new SQLConstructor();
        $c->find('orders', $filter);
        $list = $c->maps();

        $sm = new Smarty();
        $sm->setTemplateDir($this->path().'/');
        $sm->assign('orders', $list);

        return $sm->fetch('orders.tpl');
    }

    public function confirm($oid) {
        $c = new SQLConstructor();
        $ord = $c->find('orders', ['id'=>intval($oid)])->map();

        $f = new Form('Обработка заявки', ['action'=>'/admin/content/orders/']);
        $addr = new FieldTextarea(false, ['name'=>'addr', 'value'=>$ord['client']]);
        $info = new FieldTextarea(false, ['name'=>'info', 'value'=>$ord['content']]);
        $foid = new FieldHidden(['name'=>'oid', 'value'=>intval($oid)]);
        $cancel = new FieldButton('Отмена');
        $cancel->events(['onclick'=>"document.location='/admin/content/orders/';"]);

        $f->fields($addr, $info, $foid, new FieldGroup([new FieldSubmit('Закрыть заявку'), $cancel]));
        $f->callback('save');



        return $f->view();
    }

    public function save() {
        $user = User::getInstance();

        $p = $_POST;
        $c = new SQLConstructor();
        if($c->update('orders', ['content'=>$p['info'], 'uid'=>$user->id()], ['id'=>$p['oid']])->execute()) {
            Message::add('Заявка успешно обработана.');
        } else Message::add('Не удалось сохранить изменения.', Message::MESSAGE_ERROR);
    }

    public function form() {
//        $user = User::getInstance();

        $d = new Dictionary();
        $d->setCode('cfg.address');
        $builds = $d->get();
        if($builds && $builds['val'] != '') {
            $oid = $this->getOrderId();

//            var_dump($oid);
//            if($user->id() == 1) $oid = '01618';

            if($oid && !((new SQLConstructor())->find('orders', ['oid'=>$oid])->map())) {
                $db = new DB("select acc.uid, acc.login, aa.building from accounts acc left join accounts_addr aa on aa.uid=acc.uid where acc.login='".$oid."' and aa.building in (".$builds['val'].");");
                $db->openConnect('192.168.100.100', 'billing', 'sp', 'qwepoi123');

                $ud = $db->map();
//                if($user->id() == 1) $ud = ['uid'=>'1619', 'login'=>'01618', 'building'=>'1417'];

                if($ud) {
                    $db->query("select s.name as street, b.name as build, f.name as flat from accounts_addr aa
                            left join address_street s on aa.street=s.record_id
                            left join address_building b on aa.building=b.record_id
                            left join address_flat f on aa.flat=f.record_id
                            where aa.uid='".$ud['uid']."'");
                    $addr = $db->map();

                    $f = new Form('Новая система доступа', ['action'=>'/']);

                    $street = new FieldText(false, ['name'=>'street', 'placeholder'=>'Улица']);
                    $home = new FieldText(false, ['name'=>'home', 'placeholder'=>'Дом']);
                    $flat = new FieldText(false, ['name'=>'flat', 'placeholder'=>'Квартира']);
                    if($addr) {
                        $street->value($addr['street']);
                        $home->value($addr['build']);
                        $flat->value($addr['flat']);
                    }

                    $phone = new FieldText(false, ['name'=>'phone', 'placeholder'=>'Телефон']);
                    $info = new FieldTextarea(false, ['name'=>'info', 'placeholder'=>'Дополнительная информация']);

                    $f->fields($street, $home, $flat, $phone, $info, new FieldSubmit('Подать заявку'));
                    $f->callback('add');

                    return [
                        'header'=>'',
                        'template'=>'order',
                        'content'=>$f->view()
                    ];
                }
            }
        }
        return false;
    }

    private function getOrderId() {
        $user = User::getInstance();
        $ip = $_SERVER['REMOTE_ADDR'];
        if($user->id() == 1) $ip = '31.134.38.201';

        $db = new DB("select * from (select ag.number,INET_NTOA(s.segment) as ip from staff s, vgroups vg, agreements ag where vg.uid=ag.uid and vg.vg_id=s.vg_id) t where t.ip='".$ip."'");
        $db->openConnect('192.168.100.100', 'billing', 'sp', 'qwepoi123');
        $res = $db->map();

        return ($res)?$res['number']:false;
    }

    public function add() {
        $p = $_POST;
        $oid = $this->getOrderId();

        if($p['phone'] != '' && $p['street'] != '' && $p['home'] != '' && $oid) {
            $c = new SQLConstructor();
            $addr = 'ул. '.$p['street'].', дом. '.$p['home'].', кв. '.$p['flat'];
            $info = 'тел. '.$p['phone']."\n\r".$p['info'];

            $c->insert('orders', [[$addr, $info, $oid, time()]]);
            $c->fields('client', 'content', 'oid', 'date');
            if($c->execute()) Message::add('Ваша заявка успешно добавлена.', Message::MESSAGE_INFO);
            else Message::add('Не удалось сохранить заявку. Попробуйте снова или обратитесь к администрации.', Message::MESSAGE_ERROR);
        } else Message::add('Заполнены не все поля.', Message::MESSAGE_ERROR);
    }

    public function install() {
        $sql = "
            CREATE TABLE orders(
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `uid` INT(11) NOT NULL DEFAULT 0,
                `oid` VARCHAR(10) NOT NULL,
                `client` VARCHAR(255) NOT NULL,
                `content` TEXT,
                `date` INT(11) NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $db = new DB($sql);
        if($db->execute()) {
            $this->activate();
            return true;
        } else return false;
    }

    public function uninstall() {
        $this->deactivate();
        return (new DB('DROP TABLE `orders`'))->execute();
    }

    public function activate() {
        $block = new Block();
        $block->add('Заявка', 'Module_Order', 'form', ['vs'=>'', 'unv'=>''], '', 'ironnet', [], 0);

        $d = new Dictionary();
        $d->setData([
            'title'=>'Заявки',
            'parent'=>'menu.admin.content',
            'code'=>'menu.admin.content.orders',
        ]);
        $d->add();
    }

    public function deactivate() {
        $c = new SQLConstructor();
        $c->delete('blocks', ['module'=>'Module_Order']);
        $c->execute();

        $d = new Dictionary();
        $d->setCode('menu.admin.content.orders');
        $d->delete();
    }

    public function perms() {
        return [
            'order add'=>'Создание',
            'order mng'=>'Управление'
        ];
    }
}