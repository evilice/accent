<?php
/**
 * Класс для вывода дааных в табличном виде
 *
 * @author goncharovsv
 */
class ViewTable implements ViewInterface {
    private $data = [];
    private $path = '';
    
    private $rows = [];
    private $cols = [];
    private $cells = [];
    private $table = [];
    
    public function __construct() {
        $this->path = pathToModule('View');
    }

    /**
     * 
     * @param Array $data
     * @return \ViewTable
     */
    public function data($data) { $this->data = $data; return $this; }
    
    /**
     * 
     * @param Map $config
     * @return \ViewTable
     */
    public function config($config) {
        foreach ($config as $key=>$v) {
            $this->$key = $v;
        }
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function view() {
        $page = Page::getInstance();
        $page->css('/'.$this->path.'/css/ViewTable.css');
        
        $sm = new Smarty();
        
        $sm->setTemplateDir($this->path.'/tmpls/');
        $sm->assign('data', $this->data);
        $sm->assign('cells', $this->cells);
        $sm->assign('rows', $this->rows);
        $sm->assign('cols', $this->cols);
        $sm->assign('table', $this->table);

        return str_clear($sm->fetch('ViewTable.tpl'));
    }
}