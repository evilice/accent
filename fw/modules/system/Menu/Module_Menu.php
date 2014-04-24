<?php
/**
 * Description of Module_Menu
 *
 * @author goncharovsv
 */
class Module_Menu extends Module {
    private $url = 'admin/structure/menu';
    
    public function boot() {
        $p = $this->path();
        incFile($p.'/Menu.php');
    }
    
//    public function controllers() {
//        $url = $this->url;
//        return [
//            $url.'/ajax/list'=>['callback'=>'ajaxList', 'perms'=>['system menu']],
//            $url.'/ajax/save'=>['callback'=>'ajaxSave', 'perms'=>['system menu']]
//            
//            $url=>['title'=>'Управление меню', 'callback'=>'listMenu', 'perms'=>['system menu']],
//            $url.'/add'=>['title'=>'Форма добавления', 'callback'=>'addForm', 'perms'=>['system menu']],
//            $url.'/edit'=>['title'=>'Форма редактировать', 'callback'=>'editForm', 'perms'=>['system menu']],
//            $url.'/del'=>['title'=>'Форма удаления', 'callback'=>'delForm', 'perms'=>['system menu']]
//        ];
//    }
    
    /**
     * 
     */
//    public function listMenu() {
//        $p = $this->path();
//        $page = Page::getInstance();
//        
//        $page->js('fw/libs/js/jquery/tree/jquery.tree.js');
//        $page->js($p.'/js/menu.js');        
//        $page->css('/'.$p.'/css/menu.css');
//
//        $btSave = new FieldButton('Сохранить', ['id'=>'btMenuSave']);
//        $btCreate = new FieldButton('Создать', ['id'=>'btMenuCreate']);
//        
//        return $btSave->view().$btCreate->view();
//    }
    
    /**
     * Форма создания и редактирования пунктов меню
     * @param Array $map
     * @return String
     */
//    private function form($map = false) {
//        $els = [['text'=>'', 'value'=>0]];
//        $db = new SQLConstructor();
//        $list = $db->find('menu')->fields('id as value', 'name as text')->maps();
//        foreach ($list as $v) {
//            if($map && $map['pid'] == $v['value']) $v['selected']=true;
//            $els[] = $v;
//        }
//        $f = new Form(false, ['id'=>'menuAddForm', 'method'=>'post']);
//        
//        $parent = new FieldSelect('Родительский элемент', ['name'=>'parent']);
//        $parent->options($els);
//        $name = new FieldText('Название', ['name'=>'name']);
//        $url = new FieldText('Адрес', ['name'=>'url']);
//        $descr = new FieldTextarea('Описание', ['name'=>'description']);
//        $save = new FieldSubmit('Создать');
//        $del_c= new FieldButton();
//        $box = new FieldGroup();
//        
//        if($map) {
//            $f->title('Форма редактирования пункта меню');
//            $f->action($this->url.'/save');
//            
//            $name->value($map['name']);
//            $url->value($map['url']);
//            $descr->value($map['description']);
//            $f->callback('save');
//            $del_c->title('Удалить');
//            $del_c->events(['onclick'=>"g2p('../del/".$map['id']."');return false;"]);
//            $save->title('Сохранить');
//            $f->field(new FieldHidden(['name'=>'mid', 'value'=>$map['id']]));
//        } else {
//            $f->title('Форма создания пункта меню');
//            $f->action($this->url.'/add');
//            $f->callback('add');
//            $del_c->title('Отмена');
//            $del_c->events(['onclick'=>"g2p('../menu');return false;"]);
//        }
//        
//        $box->fields($save, $del_c);
//        $f->fields($parent, $name, $url, $descr, $box);
//        
//        return $f->view();
//    }
    
    /**
     * Форма создания пункта меню
     * @return String
     */
//    public function addForm() {
//        return $this->form();
//    }
    
    /**
     * Форма редактирования пункта меню
     * @return string
     */
//    public function editForm($id) {
//        $m = new Menu();
//        return $this->form($m->get((int)$id));
//    }
    
    /**
     * Форма удаления элемента меню
     * 
     * @param Integer $id
     */
//    public function delForm($id) {
//        $db = new SQLConstructor();
//        $el = $db->find('menu', ['id'=>(int)$id])->db()->map();
//        
//        $btCnl = new FieldButton('Отмена');
//        $btCnl->events(['onclick'=>"g2p('/".$this->url."');return false;"]);
//        
//        if($el) {
//            $form = new Form('Вы действительно хотите удалить пункт меню '.$el['name'].' и все связанные с ним даные?');
//            $form->action('/'.$this->url);
//            $form->callback('deleteElement');
//            
//            $mid = new FieldHidden(['name'=>'mid', 'value'=>(int)$id]);
//            $btDel = new FieldSubmit('Удалить');
//            
//            return $form->fields($mid, new FieldGroup([$btDel, $btCnl]))->view();
//        } else {
//            Message::add('Выбранный элемент не найден.', Message::MESSAGE_ERROR);
//            $btCnl->title('Назад');
//            return $btCnl->view();
//        }
//    }
    
    /**
     * Удаление элемента
     */
//    public function deleteElement() {
//        $id = (int)$_POST['mid'];
//        $db = new SQLConstructor();
//        $el = $db->find('menu', ['id'=>$id])->db()->map();
//        if($el) {
//            $del_ids = [$id];
//            $tmp_ids = [$id];
//            while($tmp_ids) {
//                $db = new SQLConstructor();
//                $tmp = $db->find('menu', ['@in'=>['pid', $tmp_ids]])->fields('id')->db()->maps();
//                $tmp_ids = [];
//                foreach ($tmp as $t) $tmp_ids[] = $t['id'];
//                $del_ids = array_merge($del_ids, $tmp_ids);
//            }
//            $db = new SQLConstructor();
//            $db->delete('menu', ['@in'=>['id', $del_ids]])->db()->execute();
//        }
//    }
    
    /**
     * Добавление пункта меню
     */
//    public function add() {
//        $p = $_POST;
//        $menu = new Menu();
//        $menu->add($p['name'], $p['url'], $p['parent'], $p['description']);
//    }
    
    /**
     * Список пунктов меню, запрашиваемый AJAX
     */
//    public function ajaxList() {
//        $menu = new Menu();
//        print json_encode($menu->tree());
//        exit();
//    }
    
    /**
     * Сохранение дерева
     */
//    public function ajaxSave() {
//        $chl = $_POST['changeList'];
//        foreach ($chl as $k=>$v) {
//            $db = new SQLConstructor();
//            $db->update('menu', ['@flds'=>['weight', 'weight+1']], [
//                '@and'=>[['pid'=>(int)$v['pid']], ['@gt'=>['weight', (int)$v['weight']-1]]]
//            ])->execute();
//            
//            $db = new SQLConstructor();
//            $db->update('menu', ['pid'=>(int)$v['pid'], 'weight'=>(int)$v['weight']], ['id'=>(int)$v['id']]);
//            $db->execute();
//        }
//        exit();
//    }
    
    public function perms() {
        return ['system menu'=>'Управление системным меню'];
    }
}