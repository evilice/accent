<?php
/**
 * Description of Form
 *
 * @author goncharovsv
 */
class Form {
    private $callback = null;
    private $formid = null;
    
    private $attrs = [];
    private $title;
    private $fields = [];
    
    public function __construct($title = false, $attributes = []) {
        $this->formid = randstr();
        if($title) $this->title = $title;
        foreach($attributes as $k=>$v) $this->attrs[$k] = $v;
        if(!isset($this->attrs['method']))
            $this->attrs['method'] = 'post';
    }
    
    /**
     * Назначает функцию - обработчик запроса
     * 
     * @param string $callback
     */
    public function callback($callback) {
        $tp = debug_backtrace();
        $this->callback = ['class'=>$tp[1]['class'], 'callback'=>$callback];
    }
    
    /**
     * Устанавливает парамтр создаваемой форме
     * 
     * @param string $name
     * @param strin $value
     * @return \Form
     */
    public function attr($name, $value) {
        $this->attrs[$name] = $value;
        return $this;
    }
    
    /**
     * Устанавливает или возвращает атрибуты
     * 
     * @param array $attrs
     * @return array
     */
    public function attrs($attrs = false) {
        if($attrs) $this->attrs = $attrs;
        else return $this->attrs;
    }
    
    /**
     * Заголовок формы
     * 
     * @param string $value
     * @return string
     */
    public function title($title = false) {
        if($title) $this->title = $title;
        else return $this->title;
    }
    
    /**
     * Устанавливает атрибут формы id
     * 
     * @param string $value
     * @return string
     */
    public function id($id = false) {
        if($id) $this->attr('id', $id);
        else return $this->attrs['id'];
    }
    
    /**
     * Устанавливает url для отправки запроса.
     * Атрибут формы action
     * 
     * @param string $value
     * @return string
     */
    public function action($url = false) {
        if($url) $this->attr('action', $url);
        else return $this->attrs['action'];
    }
    
    /**
     * Устанавливает метод отправки данных.
     * get || post
     * 
     * @param string $value
     * @return \Form
     */
    public function method($value) {
        $this->attr('method', $value);
        return $this;
    }
    
    /**
     * Добавляет поле к форме
     * 
     * @param \Field $fl
     * @return \Form
     */
    public function field($fl) {
    	$this->fields[] = $fl;
        $this->_cft($fl);
        return $this;
    }
    
    /**
     * Добавляет поля к форме.
     * Поля указываются в качестве параметром метода через зяпятую
     * 
     * @return \Form
     */
    public function fields() {
        $ls = func_get_args();
        foreach($ls as $v) {
            $this->field($v);
            $this->_cft($v);
        }
        return $this;
    }
    
    /**
     * 
     * @param type $field
     */
    private function _cft($field) {
        if(in_array(get_class($field), ['FieldFile', 'FieldImage'])) {
            $this->attr('enctype', 'multipart/form-data');
        }
    }
    
    /**
     * Возвращает список полей формы
     * 
     * @return array
     */
    public function listFields() {
        return $this->fields;
    }
    
    /**
     * Сохраняет данные о форме в сессию
     */
    private function sess() {
        if($this->callback) {
            if(!isset($_SESSION['forms'])) $_SESSION['forms']=[];
            $_SESSION['forms'][$this->formid] = $this->callback;
        }
    }
    
    /**
     * Генерирует html-код формы
     * 
     * @return string
     */
    public function view() {
        $pg = Page::getInstance();
        $sid = new FieldHidden(['name'=>'_form_uid', 'value'=>$this->formid]);
        $this->field($sid);
        $this->sess();
        
//        Cache::set(str_replace('/', '_', $_SERVER['REQUEST_URI']), 's');
        
        $sm = new Smarty();
        $sm->setTemplateDir('./templates/'.$pg->getTemplate().'/form');
        $sm->assign('form', $this);
        return $sm->fetch('form.tpl');
    } 
}