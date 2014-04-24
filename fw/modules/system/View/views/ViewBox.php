<?php
class ViewBox implements ViewInterface {
    private $header = '';
    private $content = '';
    
    public function __construct($header, $content) {
        $this->header = $header;
        $this->content = $content;
    }
    public function view() {
        $path = pathToModule('View');
        
        $page = Page::getInstance();
        $page->css('/'.$path.'/css/ViewBox.css');
        $page->js($path.'/js/ViewBox.js');
        
        $sm = new Smarty();
        $sm->setTemplateDir($path.'/tmpls/');
        $sm->assign('header', $this->header);
        $sm->assign('content', $this->content);
        
        return $sm->fetch('ViewBox.tpl');
    }
}