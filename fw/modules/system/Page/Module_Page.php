<?php
/**
 * Description of Module_Page
 *
 * @author goncharovsv
 */
class Module_Page extends Module {
    public function boot() {
        incFile($this->path().'/Page.php');
    }
}