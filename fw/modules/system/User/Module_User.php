<?php
/**
 * Description of Module_User
 *
 * @author goncharovsv
 */
class Module_User extends Module {
    public function boot() {
        $path = $this->path();
        incFiles($path.'/User.php', $path.'/sqls.php');
        User::getInstance();
    }
    
    public function init() {}
    
    public function controllers() {
        $p = 'admin/users/';
        return [
            'user/authorization'=>['title'=>'Страница авторизации', 'callback'=>'authForm', 'perms'=>[]],
            'user/logout'=>['title'=>'Выход из аккаунта', 'callback'=>'logout'],
            $p.'list'=>['title'=>'Список пользователей', 'callback'=>'user_list', 'perms'=>['user manager']],
            $p.'roles'=>['title'=>'Список ролей', 'callback'=>'rolesList', 'perms'=>['user manager']],
            $p.'perms'=>['title'=>'Права доступа', 'callback'=>'permsList', 'perms'=>['user manager']],
            $p.'create'=>['title'=>'Управление учетными записями', 'callback'=>'userAddForm', 'perms'=>['user manager']],
            $p.'edit'=>['title'=>'Редактирование учетной записи', 'callback'=>'userEditForm', 'perms'=>['user manager']],
            $p.'del'=>['title'=>'Удаление учетной записи', 'callback'=>'userDelForm', 'perms'=>['user manager']],
            $p.'roles/create'=>['title'=>'Управление учетными записями', 'callback'=>'roleAddForm', 'perms'=>['user manager']],
            $p.'role'=>['title'=>'Форма редактирования роли', 'callback'=>'roleEditForm', 'perms'=>['user manager']],
            $p.'role/delete'=>['title'=>'Форма удаления роли', 'callback'=>'roleDelForm', 'perms'=>['user manager']],
            $p.'checkName'=>['callback'=>'checkName', 'perms'=>['user manager']]
        ];
    }
    
    /**
     * Форма авторизации
     * 
     * @return string
     */
    public function authForm() {
        $f = new Form('Авторизация', ['id'=>'userAuthForm', 'method'=>'post', 'action'=>'/admin']);
        $f->field(new FieldText('Логин', ['name'=>'login']));
        $f->field(new FieldPassword('Пароль', ['name'=>'pass']));
        $f->field(new FieldSubmit('Вход'));
        $f->callback('authFormSend');

        return $f->view();
    }
    
    /**
     * Авторизация
     */
    public function authFormSend() {
        $user = User::getInstance();
        $user->authorization($_POST['login'], $_POST['pass']);
    }

    public function checkName($name) {
        $c = new SQLConstructor();
        return (int)$c->find('users', ['name'=>$name])->count();
    }
    
    public function userAdd() {
        $p = $_POST;
        $c = new SQLConstructor();
        $res = $c->insert('users', [
            [$p['login'], md5(md5($p['pass'])), time(), 1]
        ])->fields('name', 'pass', 'dreg', 'status')->execute();
        if($res) {
            $id = (int)$c->lastId();
            $roles = [];
            foreach($p['us_roles'] as $r) $roles[] = [$id, (int)$r];
            $ur = new SQLConstructor();
            $res = $ur->insert('users_roles', $roles)->fields('uid', 'rid')->execute();
            if($res) {
                Message::add('Пользователь успешно добавлен.', Message::MESSAGE_INFO);
                return true;
            }
        }
        Message::add('Внимание! При добавлении пользователя произошла ошибка.', Message::MESSAGE_ERROR);
    }
    
    /**
     * Форма создания учетной записи пользователя
     * @return String
     */
    public function userAddForm() {
        js($this->path().'/js/user.js');
        js(pathToModule('Message').'/message.js');

        $ff = new Form('Форма создания учётной записи', ['name'=>'user_form', 'id'=>'user_form']);
        $ff->attr('action', '/admin/users/list');
        $ff->callback('userAdd');
        
        $box = new FieldGroup(false, 'Роли');
        $login = new FieldText('Логин', ['name'=>'login', 'id'=>'login']);
        $pass = new FieldPassword('Пароль', ['name'=>'pass', 'id'=>'pass']);
        $rePass = new FieldPassword('Подтверждение пароля', ['id'=>'repass']);
        $bt = new FieldButton('Создать', ['id'=>'btFormUA']);
        
        $db = new SQLConstructor();
        $roles = $db->find('roles')->db()->maps();
        foreach ($roles as $r) {
            $box->fields(new FieldCheckbox($r['name'], ['value'=>$r['id'], 'name'=>'us_roles[]']));
        }
        
        $ff->fields($login, $pass, $rePass, $box, $bt);
        
        return $ff->view();
    }


    /**
     * Форма редактирования учётной записи пользователя
     *
     * @param $uid
     */
    public function userEditForm($id) {
        $id = (int)$id;
        js($this->path().'/js/user.js');
        js(pathToModule('Message').'/message.js');

        $user = (new SQLConstructor())->find('users', ['id'=>$id])->fields('name')->map()['name'];

        $f = new Form('Редактирование учётной записи `'.$user.'`', ['id'=>'user_form', 'action'=>'/admin/users/list']);
        $f->callback('userSave');

        $uid = new FieldHidden(['name'=>'uid', 'value'=>$id]);

        $userRoles = array_keys((new SQLConstructor())->find('users_roles', ['uid'=>$id])->fields('rid')->maps('rid'));
        $roles = (new SQLConstructor())->find('roles')->maps();
        $rolesBox = new FieldGroup(false, 'Роли');
        foreach($roles as $r) {
            $ch = new FieldCheckbox($r['name'], ['name'=>'us_roles[]', 'value'=>$r['id']]);
            $ch->checked(in_array($r['id'], $userRoles));
            $rolesBox->fields($ch);
        }

        $pass = new FieldPassword('Пароль', ['name'=>'pass']);
        $pass->description('Заполнить, если необходимо изменить пароль.');

        $save = new FieldSubmit('Сохранить', ['id'=>'btSaveUF']);
        $cancel = new FieldButton('Отмена', ['id'=>'btCnclUF']);
        $cancel->events(['onclick'=>"g2p('/admin/users/list'); return false;"]);
        $del = new FieldButton('Удалить');
        $del->events(['onclick'=>"g2p('/admin/users/del/".$id."'); return false;"]);

        $f->fields($uid, $pass, $rolesBox, new FieldGroup([$save, $cancel, $del]));

        return $f->view();
    }

    /**
     * Сохранение данных о пользователе
     */
    public function userSave() {
        $id = (int)$_POST['uid'];
        if($_POST['pass'] != '')
            (new SQLConstructor())->update('users', ['pass'=>md5(md5($_POST['pass']))], ['id'=>$id])->execute();
        if((new SQLConstructor())->delete('users_roles', ['uid'=>$id])->execute()) {
            $dt = [];
            foreach($_POST['us_roles'] as $r) $dt[] = [$id, $r];
            (new SQLConstructor())->insert('users_roles', $dt)->execute();
        }
    }

    /**
     * Форма удаления учетной записи
     *
     * @param $uid
     * @return string
     */
    public function userDelForm($id) {
        $user = (new SQLConstructor())->find('users', ['id'=>(int)$id])->map();
        if($user) {
            $f = new Form('Вы действительно хотите удалить учетную запись `'.$user['name'].'`', ['action'=>'/admin/users/list']);
            $f->fields(new FieldHidden(['value'=>$id, 'name'=>'uid']), new FieldGroup([new FieldSubmit('Удалить'), new FieldCancel('Отмена', '/admin/users/list')]));
            $f->callback('userDel');
            return $f->view();
        } else {
            Message::add('Учётная запись не найдена.', Message::MESSAGE_ERROR);
            g2p('/admin/users/list');
        }
    }

    /**
     * Удаление учетной записи
     */
    public function userDel() {
        $id = (int)$_POST['uid'];
        if((new SQLConstructor())->delete('users_roles', ['uid'=>$id])->execute() &&
            (new SQLConstructor())->delete('users', ['id'=>$id])->execute()) {
            Message::add('Учетная запись успешно удалена.', Message::MESSAGE_INFO);
        } else Message::add('Во время удаления учетной записи, произошла ошибка.', Message::MESSAGE_WARNING);
    }
    
    /**
     * Создание новой роли
     */
    public function roleAdd() {
        $db = new SQLConstructor();
        $db->insert('roles', [[$_POST['title'], $_POST['description']]]);
        $db->fields('name', 'description');
        if($db->db()->execute()) Message::add('Данные успешно добавлены');
        else Message::add('Не удалось сохранить данные.', Message::MESSAGE_ERROR);
    }
    
    /**
     * Форма создания роли
     */
    public function roleAddForm() {
        $form = $this->roleForm();
        $form->title('Форма создания роли');
        $form->callback('roleAdd');
        
        $bt = new FieldSubmit('Создать роль');
        $form->fields($bt);
        return $form->view();
    }
    
    /**
     * Форма редактирования роли
     * @param Integer $rid
     * @return string
     */
    public function roleEditForm($rid) {
        $db = new SQLConstructor();
        $role = $db->find('roles', ['id'=>(int)$rid])->db()->map();
        
        $form = $this->roleForm($role);
        
        $add = new FieldSubmit('Сохранить');
        $del = new FieldButton('Удалить');
        $del->events(['onclick'=>"g2p('/admin/users/role/delete/".$role['id']."'); return false;"]);
        $box = new FieldGroup();
        $box->fields($add, $del);
        
        $form->fields($box);
        
        return $form->view();
    }
    
    /**
     * Форма удаления роли
     * 
     * @param int $rid
     * @return string
     */
    public function roleDelForm($rid) {
        $db = new SQLConstructor();
        $role = $db->find('roles', ['id'=>(int)$rid])->db()->map();
        if($role) {
            $form = new Form();
            $form->title('Вы действительно хотите удалить роль `'.$role['name'].'`?');
            $form->action('/admin/users/roles');
            $form->callback('delRole');
            $del = new FieldSubmit('Удалить');
            $cancel = new FieldButton('Отмена');
            $cancel->events(['onclick'=>"g2p('/admin/users/roles');"]);
            $box = new FieldGroup();
            $box->fields($del, $cancel);
            
            $form->fields($box);
            $form->fields(new FieldHidden(['name'=>'rid', 'value'=>(int)$rid]));
            return $form->view();
        } else {
            Message::add('Роль не найдена.', Message::MESSAGE_ERROR);
        }
    }
    
    /**
     * Удаление роли
     */
    public function delRole() {
        $rid = (int)$_POST['rid'];
        $db = new SQLConstructor();
        if($db->delete('roles', ['id'=>$rid])->db()->execute()) {
            $db = new SQLConstructor();
            $db->delete('roles_perms', ['id'=>$rid])->db()->execute();
            Message::add('Роль успешно удалена.');
        }
    }
    
    /**
     * 
     * @param array $role
     * @return \Form
     */
    private function roleForm($role = false) {
        $form = new Form(false, ['action'=>'/admin/users/roles']);
        
        $name = new FieldText('Название', ['name'=>'title']);
        $desc = new FieldTextarea('Описание', ['name'=>'description']);
        if($role) {
            $name->value($role['name']);
            $desc->value($role['description']);
        }
        
        $form->fields($name, $desc);
        
        return $form;
    }
    
    /**
     * Выход из системы
     */
    public function logout() {
        $user = User::getInstance();
        $user->logout();
        
        $page = Page::getInstance();
        $page->setData('content', "<a href='/user/authorization'>Auth</a>");
    }
    
    /**
     * Список пользователей
     * 
     * @return String
     */
    public function user_list() {
        $bt = new FieldButton('Создать пользователя');
        $bt->events(['onclick'=>"g2p('/admin/users/create');"]);
        
        $res = [];
        $users = (new SQLConstructor())->find('users')->maps();
        
        foreach ($users as $u) {
            $rstr = [];
            $db = new DB(SQL::fill('user_roles', $u['id']));
            $roles = $db->maps();
            
            foreach ($roles as $k=>$v) $rstr[] = $v['name'];
            
            $res[] = [
                '<a href="/admin/users/edit/'.$u['id'].'">'.$u['name'].'</a>',
                implode(', ', $rstr),
                ($u['dreg'] != '0')?date('d.m.y', (int)$u['dreg']):'',
                ($u['lastvisit'] != '0')?date('H:i - d.m.y', (int)$u['lastvisit']):'Никогда'
            ];
        }
        
        $tb = new ViewTable();
        $tb->data(array_merge([['Имя', 'Роли', 'Дата<br />регистрации', 'Последнее посищение']], $res));
        $tb->config([
            'rows'=>['r_0'=>['class'=>'tvRowH']],
            'cells'=>[
                '0_n'=>['style'=>'width:170px;'],
                '1_n'=>['style'=>'width:250px;'],
                '2_n'=>['style'=>'width:115px;'],
                '3_n'=>['style'=>'width:115px;']
            ]
        ]);
        
        return $bt->view().$tb->view();
    }
    
    /**
     * Список ролей
     * 
     * @return string
     */
    public function rolesList() {
        $res = [];
        $db = new SQLConstructor();
        $list = $db->find('roles')->db()->maps();
        
        $res[] = ['Роль', 'Описание'];
        foreach ($list as $r) {
            $res[] = ['<a href="/admin/users/role/'.$r['id'].'">'.$r['name'].'</a>', $r['description']];
        }
        
        $tb = new ViewTable();
        $tb->data($res);
        $tb->config([
            'rows'=>['r_0'=>['class'=>'tvRowH']],
            'cells'=>[
                '0_n'=>['style'=>'width:230px;'],
                '1_n'=>['style'=>'width:435px;']]
        ]);
        
        $bt = new FieldButton('Создать роль');
        $bt->events(['onclick'=>"g2p('/admin/users/roles/create'); return false;"]);
        
        return $bt->view().$tb->view();
    }
    
    public function permsList() {
        $r_perms = [];
        $config = [
            'table'=>['class'=>'user_vt_perms'],
            'rows'=>['r_0'=>['class'=>'user_vt_header']],
            'cells'=>['0_n'=>['style'=>'width:200px;']]
        ];
        $page = Page::getInstance();
        $page->css('/'.$this->path().'/users.css');
        
        $data = [['Право доступа']];
        
        //--- Список прав
        $db = new SQLConstructor();
        $tmp = $db->find('roles_perms')->db()->maps();
        foreach ($tmp as $p) {
            $key = 'uid_'.$p['id'];
            if(!isset($r_perms[$key])) $r_perms[$key] = [];
            $r_perms[$key][] = $p['permission'];
        }
        
        //--- Список ролей
        $db = new SQLConstructor();
        $roles = $db->find('roles')->fields('id', 'name')->db()->maps();
        foreach($roles as $r) $data[0][] = $r['name'];
        
        //--- Список модулей
        $db = new SQLConstructor();
        $mods = $db->find('modules', ['status'=>1])->fields('id', 'name')->db()->maps();
        
        $row_id = 1;
        $rl_count = count($roles);
        $md_count = count($mods);
        for ($j=0; $j<$md_count; $j++) {
            $m = $mods[$j];
            $modn = 'Module_'.$m['name'];
            $mod = new $modn();
            $perms = $mod->perms();
            
            if($perms!=NULL) {
                $data[] = $m['name'];
                $config['rows']['r_'.$row_id] = ['class'=>'user_vt_mod'];
                $row_id++;
                foreach ($perms as $k=>$v) {
                    $row_id++;
                    $map = [$v];
                    for($i=0; $i<$rl_count; $i++) {
                        $rid = $roles[$i]['id'];
                        $c = new FieldCheckbox(false, ['value'=>$k, 'name'=>'role_'.$rid.'[]']);
                        $c->checked((isset($r_perms['uid_'.$rid])?(in_array($k, $r_perms['uid_'.$rid])):false));

                        $map[] = $c->view();
                    }
                    $data[] = $map;
                }
            }
        }
        
        $tb = new ViewTable();
        $tb->data($data)->config($config);
        
        $bt = new FieldSubmit('Сохранить изменения');
        
        $form = new Form(false, ['id'=>'formRolesPerms', 'method'=>'post', 'action'=>'']);
        $form->callback('save_role_perms');
        $form->fields(new FieldHtml($tb->view()), $bt);
        return $form->view();
    }
    
    public function save_role_perms() {
        $db = new SQLConstructor();
        $res = $db->delete('roles_perms')->db();
        $res->execute();

        foreach ($_POST as $k=>$v) {
            $vals = [];
            $rid = (int)substr($k, strrpos($k, '_')+1);

            foreach ($v as $p) $vals[] = [$rid, $p];
            
            $db = new SQLConstructor();
            $db->insert('roles_perms', $vals)->fields('id', 'permission')->db()->execute();
        }
        Message::add('Настройки были успешно сохранены', Message::MESSAGE_INFO);
    }
    
    public function blocks() {
        return [
            [
                'title'=>'Авторизация',
                'callback'=>'authform',
                'cache'=>true
            ]
        ];
    }
    
    public function perms() {
        return [
            'user manager'=>'Управление пользователями'
        ];
    }
}