<?php
/**
 * Description of FieldGroup
 *
 * @author SP
 */
class FieldGroup extends FormField {
    private $fields = []; //--- Поля формы
    
    public function __construct($fields = false, $title = false) {
        if($title) $this->title = $title;
        if($fields) $this->fields = $fields;
    }
    
    /**
     * Устанавливает список полей
     * 
     * @param FormField $fields
     * @return array
     */
    public function fields() {
        $flds = func_get_args();
        foreach($flds as $fl) $this->fields[] = $fl;
    }
    
    /**
     * Возвращает список полей
     * 
     * @return array
     */
    public function listFields() {
        return $this->fields;
    }
    
    /**
     * 
     */
    public function view() {
       $pg = Page::getInstance();
        $sm = new Smarty();
        $sm->setTemplateDir('./templates/'.$pg->getTemplate().'/form');
        $sm->assign('fl', $this);
        return $sm->fetch($this->type().'.tpl');
    }
}