<?php
/**
 * Конструктор SQL запросов
 *
 * @author goncharovsv
 */
class Module_DbQuery extends Module {
    public function boot() {
        $path = $this->path();
        incFile($path.'/SQLConstructor.php');
    }
}