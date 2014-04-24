<?php
/**
 * Description of Module_AdminPanel
 *
 * @author goncharovsv
 */
class Module_AdminPanel extends Module {
    public function perms() {
        return [
            'admin panel'=>'Доступ в панель управления'
        ];
    }
}