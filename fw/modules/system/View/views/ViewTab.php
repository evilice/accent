<?php
/**
 * Description of ViewTab
 *
 * @author goncharovsv
 */
class ViewTab implements ViewInterface {
    const LINE_VERTICAL = 'v';
    const LINE_HOROZONTAL = 'h';

    private $type = self::LINE_VERTICAL; //--- Тип расположения табов
    private $tabs = []; //--- Названия табов
    private $contents = []; //--- Содержимое табов

    /**
     * Устанавливает тип расположения табов
     * 
     * @param String $type
     */
    public function type($type) { $this->type = $type; }
    
    /**
     * Добавляет закладку
     * 
     * @param String $title
     * @param String $content
     */
    public function tab($title, $content) {
        $this->tabs[] = $title;
        $this->contents[] = $content;
    }
    
    /**
     * Генерация html кода
     */
    public function view() {
        $path = pathToModule('View');
        
        $page = Page::getInstance();
        $page->css('/'.$path.'/css/ViewTab.css');
        $page->js($path.'/js/ViewTab.js');
        
        $sm = new Smarty();
        $sm->setTemplateDir($path.'/tmpls/');
        $sm->assign('tabs', $this->tabs);
        $sm->assign('contents', $this->contents);
        $sm->assign('type', $this->type);
        
        return $sm->fetch('ViewTab.tpl');
    }
}