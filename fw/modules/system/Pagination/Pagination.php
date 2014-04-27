<?php
/**
 * Created by JetBrains PhpStorm.
 * User: goncharovsv
 * Date: 25.03.13
 * Time: 11:38
 * To change this template use File | Settings | File Templates.
 */
class Pagination {
    private $page = 0;
    private $pages = 0;
    private $url = '';
    private $step = 10;
    private $elements = 0;
    private $template = false;

    public function __construct($elements, $step, $url, $page = 0, $template = false) {
        $this->page = (int)$page;
        $this->step = (int)$step;
        $this->url = $url;
        $this->elements = (int)$elements;

        if($template != false) {
            $this->template = $template;
        } else {
            $page = Page::getInstance();
            $tpl = pathToTemplate($page->getTemplate());
            if(file_exists($tpl.'/pagination.tpl')) $this->template = $tpl;
            else $this->template = pathToModule('Pagination').'/files';
        }

        if($this->step == 0 || $this->elements == 0) {
            $this->pages = 1;
        } else {
            $ost = $this->elements%$this->step;
            $this->pages = ($this->elements-$ost)/$this->step+($ost>0?1:0);
        }
    }

    public function view() {
        if($this->pages > 1) {
            $sm = new Smarty();
            $sm->setTemplateDir($this->template);

            $sm->assign('elements', $this->elements);
            $sm->assign('pages', $this->pages);
            $sm->assign('page', $this->page);
            $sm->assign('url', $this->url);

            return $sm->fetch('pagination.tpl');
        } else return '';
    }
}
