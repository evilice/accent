<?php
class Module {
    protected function path() {
    	$pth = 'fw/modules/';
        list($tp, $nm) = explode('_', get_called_class());
        if(is_dir($pth.'system/'.$nm)) { return $pth.'system/'.$nm; }
        else { return $pth.'all/'.$nm; }
    }
    
    public function install() {}
    public function uninstall() {}
    public function activate() {}
    public function deactivate() {}
    
    public function boot() {}
    public function init() {}
    public function controllers() { return []; }
    public function build() {}
    public function end() {}

    public function cron() {}
    
    public function perms() {}
}