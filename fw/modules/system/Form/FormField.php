<?php
/**
 * Description of FormField
 *
 * @author goncharovsv
 */
class FormField implements FormFieldInterface {
    protected  $fid; // Идентификатор поля в БД
    protected $upTitle; // Верхний заголовок
    protected $title; // Заголовок
    protected $description; // Описание
    protected $attrs = []; // Атрибуты
    
    public function __construct($title = false, $attributes = false) {
        if($title) $this->title = $title;
        if($attributes) $this->attrs = $attributes;
    }

    public static function listners() {}
    
    /**
     * Возвращет / устанавливает идентификатор поля
     * 
     * @param type $val
     * @return type
     */
    public function fid($val = false) {
        if($val) $this->fid = strtolower($val);
        else return $this->fid;
    }

    /**
     * Устанавливает или возвращает атрибуты
     * 
     * @param array $attrs
     * @return array
     */
    public function attrs($attrs = false) {
        if($attrs)
            foreach ($attrs as $key => $val)
                $this->attrs[$key] = $val;
        else return $this->attrs;
    }

    /**
     * Возвращает значение абрибута "$attribute"
     * 
     * @param string $attribute
     * @return string
     */
    public function get($attribute) {
        return ($this->attr[$attribute])?$this->attr[$attribute]:false;
    }

    /**
     * Устанавливает или возвращает значение атрибута id
     * 
     * @param string $id
     * @return string
     */
    public function id($id = false) {
        if($id) $this->attrs['id'] = $id;
        else return $this->attrs['id'];
    }

    /**
     * Устанавливает или возвращает значение атрибута name
     * 
     * @param string $name
     * @return string
     */
    public function name($name = false) {
        if($name) $this->attrs['name'] = $name;
        else return $this->attrs['name'];
    }

    /**
     * Устанавливает или возвращает значение title
     * 
     * @param string $title
     * @return string
     */
    public function title($title = false) {
        if($title) $this->title = $title;
        else return $this->title;
    }
    
    public function upTitle($title = false) {
        if($title) $this->upTitle = $title;
        else return $this->upTitle;
    }

    /**
     * Возвращает тип поля ( == класс объекта)
     * 
     * @return string
     */
    public function type() { return get_called_class(); }

    public function value($value = false) {
        if($value) $this->attrs['value'] = $value;
        else return (isset($this->attrs['value'])?$this->attrs['value']:'');
    }
    
    /**
     * Устанавливает или возвращает описание поля (подсказка)
     * 
     * @param String $description
     * @return String
     */
    public function description($description = false) {
        if($description) $this->description = $description;
        else return ($this->description)?$this->description:'';
    }
    
    /**
     * Возвращает сгенерированный html формы
     * 
     * @return string
     */
    public function view() {
        $pg = Page::getInstance();
        $sm = new Smarty();
        $sm->setTemplateDir('./templates/'.$pg->getTemplate().'/form');
        $sm->assign('f', $this);
        return $sm->fetch($this->type().'.tpl');
    }
    
    /**
     * Форма настройки поля при прикреплении его к типу документа
     * 
     * @return array fields
     */
    public function config() {
        return false;
    }
    
    /**
     * Создание таблицы в БД при прикрплении нового поля к типу документа
     * 
     * @return boolean
     */
    public function createTable($fid) {
        return true;
    }
    
    /**
     * Сохраняет данные поля, прикреплённого к документу
     * 
     * @param int $docId
     * @param string $fid
     * @param array $data
     * @return boolean
     */
    public static function saveData($docId, $fid, $data) {
       return false; 
    }
    
    /**
     * 
     * @param Array $data
     */
    public function build($data) {
        $this->title($data['title']);
        $this->name($data['fid']);
        $this->fid($data['fid']);
        if(isset($data['value'])) $this->value ($data['value']);
    }
    
    /**
     * Удалить все данные связанные с документом
     * 
     * @param int $doc
     */
//    public function delete($doc) {
//        if($this->fid) {
//            $c = new SQLConstructor();
//            $c->delete($this->fid, ['doc'=>(int)$doc]);
//            return $c->execute();
//        } else return false;
//    }

    public static function deleteValues($fid, $ids) {}
    
    /**
     * Информация о поле
     * @return string/boolean
     */
    public function info() {
        return false;
    }
}