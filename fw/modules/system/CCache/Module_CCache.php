<?php
/**
 * Управление блоками
 *
 * @author goncharovsv
 */
class Module_CCache extends Module {
    private $p = 'admin/config/ccache';
    
    public function init() {
        $d = new Dictionary();
        $d->setCode('cfg.ccache');
        $chs = $d->childs();
        foreach($chs as $it)
            Cache::setConfig ($it['code'], $it['val']);
    }
    
    private function dc($code, $title, $parent='', $val='') {
        $d = new Dictionary();
        $d->setData([
            'code'=>$code,
            'title'=>$title,
            'parent'=>$parent,
            'val'=>$val
        ]);
        $d->add();
    }
    
    /**
     * Установка модуля.
     */
    public function install() {
        //--- Прописываем в системное меню
        $this->dc('menu.admin.config.ccache', 'Кеширование', 'menu.admin.config');
        
        //--- Добавляем элементы настройек в системный словарь
        $c = 'cfg.ccache';
        $this->dc('cfg', 'Настройки');
        $this->dc($c, 'Кеширование', 'cfg');
        $this->dc($c.'.agress', 'Агрессивное кеширование', $c);
        $this->dc($c.'.page', 'Кеширование страниц', $c);
        $this->dc($c.'.block', 'Кеширование блоков', $c);
        $this->dc($c.'.view', 'Кеширование представлений', $c);
    }
    
    public function controllers() {
        $p = $this->p;
        return [
            $p=>['title'=>'Кеширование', 'callback'=>'page', 'perms'=>['ccache']]
        ];
    }
    
    public function page() {
        $f = new Form(false, ['active'=>'', 'id'=>'ccacheForm']);
        $f->callback('save');
        
        $d = new Dictionary();
        $d->setCode('cfg.ccache');
        $list = $d->childs();
        foreach($list as $l) {
            $ch = new FieldCheckbox($l['title'], ['name'=>$l['code']]);
            $ch->checked(($l['val']==''?false:true));
            $f->field($ch);
        }
        
        $f->field(new FieldHidden(['id'=>'ccacheClear', 'name'=>'ccacheClear']));
        $sv = new FieldSubmit('Сохранить');
        $cl = new FieldButton('Отчистить кеш');
        $cl->events(['onclick'=>"$('#ccacheClear').val('true'); $('#ccacheForm').submit(); return false;"]);
        $gp = new FieldGroup([$sv, $cl]);
        
        $f->field($gp);
        
        return $f->view();
    }
    
    public function save() {
        if($_POST['ccacheClear'] == 'true') {
            Cache::clear();
            Cache::cleardb();
        } else {
            $p = $_POST;
            $c = new SQLConstructor();
            $c->update('dictionary', ['val'=>''], ['parent'=>'cfg.ccache'])->execute();
            
            unset($p['ccacheClear']); 
            $keys = array_map(function($itm){
                return str_replace('_', '.', $itm);
            }, array_keys($p));
            $u = new SQLConstructor();
            $u->update('dictionary', ['val'=>'1'], ['@in'=>['code', $keys]]);
            $u->execute();
        }
    }
    
    /**
     * Список правил доступа
     * 
     * @return array
     */
    public function perms() { return ['ccache'=>'Кеширование']; }
}