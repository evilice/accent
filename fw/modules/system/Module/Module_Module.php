<?php
/**
 * Description of Module_Module
 *
 * @author goncharovsv
 */
class Module_Module extends Module {
    public function controllers() {
        return [
            'admin/modules'=>['title'=>'Система управления модулями', 'callback'=>'modules', 'perms'=>['system modules']],
            'admin/modules/status'=>['callback'=>'changeModuleStatus', 'perms'=>['system modules']],
            'admin/modules/uninstall'=>['callback'=>'formDeleteModule', 'perms'=>['system modules']]
        ];
    }
    
    /**
     * Страница управления модулями.
     * 
     * @return string
     */
    public function modules() {
        $page = Page::getInstance();
        $page->js($this->path().'/modules.js');
        
        $db = new SQLConstructor();
        
        $mds = ['system'=>[], 'all'=>[]]; //--- Готовый к выводу список модулей
        $md_sdir = read_dir('fw/modules/system'); //--- системные модули
        $md_adir = read_dir('fw/modules/all');  //--- пользовательские модули
        $md_dbt = $db->find('modules')->db()->maps(); //--- Данные об установленных модулях из БД
        $md_db = [];
        
        array_walk($md_sdir, function(&$item){ $item = [$item, 'system']; });
        array_walk($md_adir, function(&$item){ $item = [$item, 'all']; });
        foreach ($md_dbt as $t) { $md_db[$t['name']] = $t; }
        unset($md_dbt);
        
        $md_temp = array_merge($md_sdir, $md_adir);
        
        /**
         * Обрабатываем данные о мудулях
         */
        foreach ($md_temp as $d) {
            $info = json_decode(fileRead('fw/modules/'.$d[1].'/'.$d[0].'/info.json'));
            if($info) {
                $map = [];
                if(!isset($mds[$d[1]][$info->package])) $mds[$d[1]][$info->package] = [];
                if(isset($md_db[$info->name])) {
                    $info->status = (int)$md_db[$info->name]['status'];
                    $info->installed = true;
                } else {
                    $info->status = 0;
                    $info->installed = false;
                }
                $mds[$d[1]][$info->package][] = [
                    '<input type="checkbox" name="modules[]" value="'.$info->name.'" '.($info->status?'checked':'').' />',
                    $info->name,
                    $info->description
                ];
            }
        }
        
        $sm = new Smarty();
        $sm->setTemplateDir($this->path().'/');
        $sm->assign('systems', $this->mod2table($mds['system']));
        $sm->assign('clients', $this->mod2table($mds['all']));
        
        return $sm->fetch('modules.tpl');
    }
    
    private function mod2table($list) {
        $res = '';
        $conf = [
            'rows'=>['r_0'=>['class'=>'tvRowH']],
            'cells'=>[
                '0_0'=>['style'=>'text-align:center;'],
                '0_n'=>['style'=>'width:20px;'],
                '1_n'=>['style'=>'width:180px;'],
                '2_n'=>['style'=>'width:448px;']
            ]];

        foreach ($list as $packege=>$mods) {
            $t = new ViewTable();
            $tmp= $t->config($conf)
                    ->data(array_merge([['#', 'Название', t('Description')]], $mods))
                    ->view();
            $box = new ViewBox($packege, $tmp);
            $res .= $box->view();
        }
        return $res;
    }
    
    /**
     * Изменение статуса модуля
     */
    public function changeModuleStatus() {
        $md = $_POST['module'];
        $status = (int)$_POST['status'];
        
        $c = new SQLConstructor();
        $res = $c->find('modules', ['name'=>$md])->map();
        if($res) {
            $db = new SQLConstructor();
            $db->update('modules', ['status'=>$status], ['name'=>$md]);
            $db->db()->execute();

            $m = 'Module_'.$md;
            if($status) {
                incFile(pathToModule($_POST['module']).'/'.$m.'.php');
                $m = new $m();
                $m->activate();
            }
            else {
                $m = new $m();
                $m->deactivate();
            }
        } else if($status) $this->installMod ($md);
        exit();
    }
    
    /**
     * 
     * @param type $module
     */
    private function installMod($module) {
        $path = pathToModule($module);
        $m = json_decode(fileRead($path.'/info.json'));
        $m->type = (strpos($path, '/system/') !== FALSE)?'system':'all';
        
        $c = new SQLConstructor();
        $c->insert('modules', [[$m->name, $m->package, $m->type, 1]]);
        $c->fields('name', 'package', 'type', 'status')->db()->execute();
        
        $name = 'Module_'.$m->name;
        incFile($path.'/'.$name.'.php');
        $md = new $name();
        $md->install();
    }

    public function formDeleteModule($mod) {
        return "sdfasdfasdf";
    }
    
    public function perms() {
        return [
            'system modules'=>'Управлениие модулями'
        ];
    }
}