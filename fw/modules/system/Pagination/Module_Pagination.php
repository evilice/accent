<?php
/**
 * Created by JetBrains PhpStorm.
 * User: goncharovsv
 * Date: 25.03.13
 * Time: 11:36
 * To change this template use File | Settings | File Templates.
 */
class Module_Pagination extends Module {
    public function boot() {
        incFile($this->path().'/Pagination.php');
    }
    public function init() {
        $page = Page::getInstance();
        $page->js($this->path().'/files/pagination.js');
        $page->css('/'.$this->path().'/files/pagination.css');
    }
}
