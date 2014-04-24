<?php
class Module_Message extends Module {
    /**
     * Загрузка модуля
     */
    public function boot() {
        incFile($this->path().'/Message.php');
        incFile($this->path().'/sqls.php');
    }
    
    /**
     * Инициализация
     */
    public function init() {
        $page = Page::getInstance();
        $page->js($this->path().'/message.js');
        Message::init();
    }
    
    /**
     * Завершение процесса работы
     */
    public function end() {
        Message::save();
    }
}