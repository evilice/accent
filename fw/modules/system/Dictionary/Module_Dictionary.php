<?php
/**
 * Справочник
 *
 * @author goncharovsv
 */
class Module_Dictionary extends Module {
    private $url = 'admin/structure/dictionary';
    
    /**
     * Загрузка модуля
     */
    public function boot() {
        $p = $this->path();
        incFile($p.'/Dictionary.php');
        incFile($p.'/sqls.php');
    }
    
    /**
     * Контроллеры
     * 
     * @return array
     */
    public function controllers() {
        $u = $this->url;
        return [
            $u=>['title'=>'Словарь', 'callback'=>'dictionary', 'perms'=>['dictionary']],
            $u.'/ajax'=>['title'=>'AJAX запрсы', 'callback'=>'ajax', 'perms'=>[]]
        ];
    }
    
    /**
     * Страница управления словарём
     * 
     * @return string
     */
    public function dictionary() {
        //--- Подключение JS и CSS
        $path = $this->path();
        $page = Page::getInstance();
        $page->css('/'.$path.'/dictionary.css');
        $page->js($path.'/dictionary.js');
        $page->jstmpls('dc_table_row', fileRead($path.'/tmpls/dc_table_row.tpl'));        
        $page->jstmpls('dc_breadcrumbs', fileRead($path.'/tmpls/dc_breadcrumbs.tpl'));        
        
        $form = new Form();
        $form->id('form_add_dictionary');
        $code = new FieldText(false, ['id'=>'new_code']);
        $code->upTitle('Код');
        $title = new FieldText(false, ['id'=>'new_title']);
        $title->upTitle('Название');
        $val = new FieldTextarea('Значение', ['id'=>'new_value']);
        $add = new FieldButton('Добавить', ['id'=>'bt_add_new']);
        $box = new FieldGroup([$code, $title]);
        $form->fields($box, $val, $add);
        
        $cn = fileRead($path.'/tmpls/content.htm');
        
        return $cn.$form->view();
    }
    
    /**
     * Контроллер AJAX-запросов
     * 
     * @param string $act
     */
    public function ajax($act) {
        $res = false;
        if(in_array($act, ['add', 'childs', 'save', 'del', 'weight']))
            $res = $this->$act();
        echo $res; exit();
    }
    
    /**
     * Создание термина
     * 
     * @return boolean
     */
    public function add() {
        $dc = new Dictionary();
        $dc->setData($_POST);
        $dc->add();
        return true;
    }
    
    /**
     * Удаление термина
     */
    public function del() {
        $dc = new Dictionary();
        $dc->setCode($_POST['code']);
        echo ($dc->delete())?'ok':'error';
    }
    
    /**
     * Список потомков
     * 
     * @return string
     */
    public function childs() {
        $dc = new Dictionary();
        $dc->setCode($_POST['parent']);
        return json_encode($dc->childs());
    }
    
    /**
     * Изменение веса записи
     * 
     * @return boolean
     */
    public function weight() {
        $fl = true;
        foreach ($_POST['data'] as $key=>$val) {
            $c = new SQLConstructor();
            $c->update('dictionary', ['weight'=>$val], ['code'=>$key]);
            if($c->execute()) $fl=false;
        }
        return $fl;
    }
    
    /**
     * Сохраняет изменения
     * 
     * @return DB-result
     */
    public function save() {
        $d = new Dictionary();
        $d->setData($_POST);
        return (int)$d->save();
    }
    
    /**
     * Правила доступа
     * 
     * @return array
     */
    public function perms() {
        return [
            'dictionary'=>'Управление справочником'
        ];
    }
}