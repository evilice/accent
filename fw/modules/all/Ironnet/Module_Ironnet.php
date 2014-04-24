<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14.04.14
 * Time: 12:27
 */

class Module_Ironnet extends Module {
    public function boot() {
        incFile($this->path().'/Ironnet.php');
    }
} 