<?php
/**
 * Управление блоками
 *
 * @author goncharovsv
 */
class Module_Block extends Module {
    private $p = 'admin/structure/blocks';
    
    public function boot() {
        incFile($this->path().'/Block.php');
    }
    
    public function controllers() {
        $p = $this->p;
        return [
            $p=>['title'=>'Блоки', 'callback'=>'blocks', 'perms'=>['block']]
        ];
    }
    
    public function build() {}
    
    /**
     * Список блоков и места их расположения
     */
    public function blocks($tmpl = false, $blockId = false) {
        $page = Page::getInstance();
        $sm = new Smarty();
        $sm->setTemplateDir($this->path().'/');
        if($blockId) {
            $block = new Block($blockId);
            
            $f = new Form('Управление блоком "'.$block->getTitle().'"', ['action'=>'/'.$this->p.'/'.$tmpl]);
            $f->callback('save');
            $f->field(new FieldHidden(['name'=>'id', 'value'=>$block->getId()]));
            
            $pls = new FieldSelect('Место расположения', ['name'=>'place']);
            $page = Page::getInstance();
            
            $opts = $page->getTemplateInfo($tmpl)->places;
            $pls->option('---', '');
            foreach ($opts as $key=>$val)
                $pls->option($val, $key, ($key == $block->getPlace()));
            
            $f->fields(
                new FieldText('Заголовок', ['name'=>'title', 'value'=>$block->getTitle()]),
                $pls,
                new FieldTextarea('Адреса по которым будет доступен блок', ['name'=>'vs', 'value'=>implode("\n", $block->getUrls()['vs'])]),
                new FieldTextarea('Адреса по которым не будет доступен блок', ['name'=>'unv', 'value'=>implode("\n", $block->getUrls()['unv'])]),
                new FieldText('Вес', ['name'=>'weight', 'value'=>$block->getWeight()]),
                new FieldGroup([new FieldSubmit('Сохранить'), new FieldCancel('Отмена', '/'.$this->p.'/'.$tmpl)])
            );
            
            return $f->view();
            
        } else if($tmpl) {
            $info = $page->getTemplateInfo($tmpl);
            $place = [];
            $free = [];
            foreach($info->places as $k=>$v)
                $place[$k] = ['title'=>$v, 'blocks'=>[]];
            $c = new SQLConstructor();
            $c->find('blocks', ['template'=>$tmpl])->sort('weight');
            $blocks = $c->maps();
            foreach($blocks as $b) {
                $nm = $b['place'];
                if(isset($place[$nm])) {
                    $place[$nm]['blocks'][] = $b;
                } else $free[] = $b;
            }
            
            $sm->assign('url', url());
            $sm->assign('places', $place);
            $sm->assign('blocks', $free);
            return $sm->fetch('blocks.tpl');
        } else {
            $p = 'templates';
            $tpls = [];
            $list = read_dir($p);
            foreach ($list as $l) {
                $inf = $page->getTemplateInfo($l);
                $tpls[] = [
                    'url'=>'/'.$this->p.'/'.$l,
                    'name'=>$inf->name,
                    'description'=>$inf->description,
                    'screen'=>$p.'/'.$l.'/'.$inf->screen
                ];
            }
            $sm->assign('templates', $tpls);
            return $sm->fetch('themes.tpl');
        }
    }
    
    public function save() {
        $block = new Block();
        $block->data($_POST);
        if($block->save()) Message::add ('Данные успешно сохранены.', Message::MESSAGE_INFO);
    }
    
    /**
     * Список правил доступа
     * 
     * @return array
     */
    public function perms() { return ['block'=>'Управление блоками']; }
}