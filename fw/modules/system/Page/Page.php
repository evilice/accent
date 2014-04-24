<?php
/**
 * Description of Page
 *
 * @author goncharovsv
 */
class Page {
    private static $instance = null;
    private $template = null;
    private $places = [];
    private $js = [];
    private $css = [];
    private $title = '';
    private $keywords = '';
    private $description = '';
    private $data = [];
    private $jstmpls = [];
    
    private function __construct() {
        $d = new Dictionary();
        $conf = $d->tree('cfg.templates');
        $client = $conf['cfg.templates.client']['val'];
        $admin = $conf['cfg.templates.admin']['val'];
        
        $this->template = (args(0)=='admin')?$admin:$client;
        
        $tpl = pathToTemplate($this->template);
        $info = json_decode(fileRead($tpl.'/info.json'));
        
        if($info && isset($info->files)) {
            $f = $info->files;
            if($f->js) $this->js = $f->js;
            if($f->css) $this->css = $f->css;
        }
    }
    
    /**
     * 
     * @return \Page
     */
    public static function getInstance() {
        if(self::$instance == null) self::$instance = new Page();
        return self::$instance;
    }
    
    /**
     * Возвращает имя выбранного шаблона
     * 
     * @return String
     */
    public function getTemplate() { return $this->template; }
    
    /**
     * Информация о шаблоне
     * 
     * @return Map
     */
    public function getTemplateInfo($tmpl = false) {
        $p = pathToTemplate(($tmpl)?$tmpl:$this->template).'/info.json';
        return json_decode(fileRead($p));
    }
    
    /**
     * 
     * @param String $title
     * @return \Page
     */
    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    /**
     * @param $keywords
     * @return Page
     */
    public function setKeyWords($keywords) {
        $this->keywords = $keywords;
        return $this;
    }

    /**
     * @param $description
     * @return Page
     */
    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }
    
    /**
     * Добавляет JS файл
     * 
     * @return \Page
     */
    public function js() {
        $args = func_get_args();
        while($args) $this->js[] = array_pop($args);
        return $this;
    }
    
    private function parseFiles($ls) {
        $t = ['tpl'=>pathToTemplate($this->template), 'lib'=>'fw/libs/js'];
        $ls = array_unique($ls);
        array_walk($ls, function(&$item, $k, $t){
            $item = str_replace('{tpl}', $t['tpl'], $item);
            $item = str_replace('{lib}', $t['lib'], $item);
        }, $t);
        return $ls;
    }
    
    private function getJS() {
        $list = $this->parseFiles($this->js);
        if(Cache::config('cfg.ccache.jscss')) {
            $key = md5(serialize($list));
            if(!file_exists('fw/cache/'.$key.'.js')) {
                $str = '';
                foreach($list as $l) $str .= fileRead($l);
                fileWrite('fw/cache/'.$key.'.js', $str);
            }
            return $list = ['fw/cache/'.$key.'.js'];

        } else return $list;

//        return $this->parseFiles($this->js);
    }
    
    /**
     * Добавляет CSS файл
     * 
     * @return \Page
     */
    public function css() {
        $args = func_get_args();
        while($args) $this->css[] = array_pop($args);
        return $this;
    }
    
    private function getCSS() {
        $list = $this->parseFiles($this->css);
        if(Cache::config('cfg.ccache.jscss')) {
            $key = md5(serialize($list));
            if(!file_exists('fw/cache/'.$key.'.css')) {
                $str = '';
                foreach($list as $l) $str .= fileRead(substr($l, 1));
                fileWrite('fw/cache/'.$key.'.css', $str);
            }
            return $list = ['/fw/cache/'.$key.'.css'];

        } else return $list;
    }

    /**
     * 
     * @param String $key
     * @param Object $data
     * @return \Page
     */
    public function setData($key, $data) {
        $this->data[$key] = $data;
        return $this;
    }

    /**
     * Добавляет шаблоны для работы в JS (AJAX)
     * @param String $tmpl
     * @return \Page
     */
    public function jstmpls($key, $tmpl) { $this->jstmpls[$key] = $tmpl; return $this; }

    public function view(){
        $sm = new Smarty();
	    $sm->setTemplateDir('templates/'.$this->template.'/');

        $sm->assign('js', $this->getJS());
        $sm->assign('css', $this->getCSS());
        $sm->assign('jstmpls', $this->jstmpls);
        $sm->assign('metaKeywords', $this->keywords);
        $sm->assign('metaDescription', $this->description);

        foreach ($this->data as $key=>$value) {
            $sm->assign($key, $value);
        }
        
        $sm->assign('menu', new Menu());
        $sm->assign('dictionary', new Dictionary());
        
        /**
         * Построение главного меню
         */
        $m = new Menu();
        $list = $m->items('menu.'.((args(0) == 'admin')?'admin':'main'), true);
        $sm->assign('mainMenu', $list);
        
        /**
         * Breadcrumbs
         */
        $sm->assign('breadcrumbs', $m->breadcrumbs());
        
        /**
         * Построение дополнительного меню
         */
        $sm->assign('leftMenu', array(
            array('title'=>'Страницы', 'link'=>'#'),
            array('title'=>'Модули', 'link'=>'#'),
            array('title'=>'Блоки', 'link'=>'#'),
            array('title'=>'SQL-запросы', 'link'=>'#')
        ));
        
        $b = new Block();
        $blockList = $b->view($this->template);
        foreach ($blockList as $key=>$v) {
            $sm->assign($key, $v);
        }
        
        /**
         * ????
         */
        $sm->assign('leftbox');
        $sm->assign('messages', Message::get());

        $sm->assign('url', args(0));
        
        /**
         * Построение breadcrumbs
         */
//        $sm->assign('breadcrumbs', $m->breadcrumbs());

        $sm->assign('title', 'Admin panel');
        $sm->assign('tpl_path', 'templates/'.$this->template);

        return $sm->fetch('page.tpl');
    }
}
