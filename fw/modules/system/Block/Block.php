<?php
/**
 * Description of Block
 *
 * @author goncharovsv
 */
class Block {
    private $id;
    private $title;
    private $module;
    private $callback;
    private $urls;
    private $place;
    private $template;
    private $conf;
    private $weight;
    
    public function getId() { return $this->id;}
    public function getTitle() { return $this->title;}
    public function getModule() { return $this->module;}
    public function getCallback() { return $this->callback;}
    public function getUrls() {
        $res = unserialize($this->urls);
        $res['vs'] = ($res['vs']=='')?[]:$res['vs'];
        $res['unv'] = ($res['unv']=='')?[]:$res['unv'];
        return $res;
    }
    public function getPlace() { return $this->place;}
    public function getTemplate() { return $this->template;}
    public function getConf() { return $this->conf;}
    public function getWeight() { return $this->weight;}
    
    public function setUrls($urls) {
        $this->urls = serialize([
            'vs'=>$urls['vs']!=''?preg_split('/\\r\\n?|\\n/', $urls['vs']):'',
            'unv'=>$urls['unv']!=''?preg_split('/\\r\\n?|\\n/', $urls['unv']):''
        ]);
    }
    
    public function data($data) {
        foreach ($data as $key=>$val)
            if(!in_array($key, ['vs', 'unv'])) $this->$key = $val;
        $this->setUrls(['vs'=>$data['vs'], 'unv'=>$data['unv']]);
    }
    
    public function __construct($id = false) {
        if($id) {
            $c = new SQLConstructor();
            $bl = $c->find('blocks', ['id'=>$id])->map();
            foreach ($bl as $key=>$val) $this->$key = $val;
        }
    }
    /**
     * Создание блока
     * 
     * @param string $title
     * @param string $module
     * @param string $callback
     * @param array $urls
     * @param string $place
     * @param string $template
     * @param array $conf
     * @param int $weight
     * 
     * @return DB-result
     */
    public function add($title, $module, $callback, $urls=false, $place=false, $template=false, $conf=false, $weight=false) {
        $c = new SQLConstructor();
        
        $data = [$title, $module, $callback];
        $c->fields('title', 'module', 'callback');
        if($urls) { $data[] = serialize($urls); $c->fields('urls'); }
        if($place) { $data[] = $place; $c->fields('place'); }
        if($template) { $data[] = $template; $c->fields('template'); }
        if($conf) { $data[] = $conf; $c->fields('conf'); }
        if($weight) { $data[] = $weight; $c->fields('weight'); }
        
        $c->insert('blocks', [$data]);
        return $c->execute();
    }
    
    /**
     * Сохранение изменений
     * 
     * @return DBresult
     */
    public function save() {
        $fields = [
            'title'=>$this->title,
            'urls'=>$this->urls,
            'place'=>$this->place,
            'weight'=>$this->weight
        ];
        return (new SQLConstructor())->update('blocks', $fields, ['id'=>$this->getId()])->execute();
    }
    
    /**
     * Возвращает список блоков для указанного шаблона
     * 
     * @param string $tmpl
     * @return array
     */
    public function blocks($tmpl = false) {
        $c = new SQLConstructor();
        $c->find('blocks')->sort('`weight`');
        if($tmpl) $c->where(['template'=>$tmpl]);
        return $c->maps();
    }
    
    /**
     * Получает html-код блоков и распределет их по местам вывода(place)
     * 
     * @param string $tmpl
     * @return string
     */
    public function view($tmpl) {
        $res = [];
        $list = $this->blocks($tmpl); //--- Список блоков по шаблону
        foreach($list as $b) {
            $sm = new Smarty();
            $sm->setTemplateDir(pathToTemplate($tmpl).'/');

            //--- Проверяем возможность вывода блока по url-у
            if(!$this->checkUrls($b['urls'], $b['id'])) continue;
            $pl = $b['place'];
            if(!isset($res[$pl])) $res[$pl] = '';
    
            /**
             * Извлечение блока из кеша, если надо
             */
            $key = $tmpl.'_'.$b['place'].'_'.$b['id'];
            $ch = false;
            if(Cache::config('cfg.ccache.block')) $ch = Cache::get($key);
            /**
             * Создаём экземпляр модуля, установленного в качестве ответственного
             * за вывод блока. Вызываем метод модуля, указанный в качесте колбэка
             * и передаём ему настроки блока.
             */
            $html = '';
            if(!$ch) {
                $m = new $b['module']();
                $block = $m->$b['callback']($b['conf']);
                if($block) {
                    $sm->assign('header', $block['header']);
                    $sm->assign('content', $block['content']);
                    if(isset($block['template']) && file_exists(pathToTemplate($tmpl).'/block_'.$block['template'].'.tpl')) {
                        $html = $sm->fetch('block_'.$block['template'].'.tpl');
                    } else {
                        $html = $sm->fetch('place_'.$pl.'.tpl');
                    }
                    if(Cache::config('cfg.ccache.block')) Cache::set ($key, $html);
                }
            } else $html = $ch;
            
            $res[$pl] .= $html;
        }
        
        //--- Кешируем результат
        return $res;
    }
    
    /**
     * Проверка возможности вывода блока по url-у
     * 
     * @param array $urls
     * @return boolean
     */
    private function checkUrls($urls) {
        $res = false;
        $urls = ($urls)?unserialize($urls):false;
        if($urls) {
            $res = ($this->checkUrl($urls['vs']) &&
               (!$this->checkUrl($urls['unv']) || $urls['unv']==''));
        } else $res = true;
        return $res;
    }
    
    /**
     * Проверка совпадения переданных url-ов c системным
     * 
     * @param array $urls
     * @return boolean
     */
    private function checkUrl($urls) {
        $u = url();
        $res = false;
        if($urls) {
            foreach ($urls as $v) {
                if(strpos($v, '*')) {
                    $tmp = substr($v, 0, -2);
                    if(substr($u, 0, strlen($tmp)) == $tmp) $res = true;
                } else if($v == $u) return true;
            }
        } else $res = true;
        return $res;
    }
}