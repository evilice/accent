<?php
class System {
    private static $instance = null;
    private $modules = array();
    private $location = [];
    private $ajax = false;
    private $ctrlUrl = ''; //--- URL по которому был вызван контроллер
    private $cron = false;
    
    private function __construct($cron = false) { $this->cron = $cron; }
    private function __clone() {}
    
    public function isAJAX() { return $this->ajax; }

    /**
     * Singletone
     * @return \System
     */
    public static function getInstance($cron = false) {
        if(!self::$instance) self::$instance = new System ($cron);
        return self::$instance;
    }
    
    /**
     * Старт системы
     */
    public function run() {
        $url = url();
        if($url != '') {
            foreach(['.ttf', '.css', '.jpg', '.png', '.gif', '.js'] as $e)
                if(substr($url, strlen($e)*-1) == $e) return false;
        }
        $this->boot();

        $dbg = new Debug();
        $dbg->time_start();

        $this->init();
        if($this->cron) $this->call('cron');
        else $this->build();
        $this->call('end');

//        print '<!-- DB: '.Debug::getCounter('db').' -->';
//        print '<!-- Time: '.$dbg->time_stop().' -->';
    }
    
    /**
     * Подключение, необходимых для работы системы,
     * библиотек, модулей, файлов.
     */
    public function boot() {
        incFiles('fw/core/sqls/system.php');
        $db = new DB();
        $db->connect();
        
        /**
         * Подключение библиотек
         */
        incFile('fw/libs/php/Smarty/Smarty.class.php');
        incFile('fw/libs/php/Img/Img.php');

        /**
         * Подключение файла локализации
         */
        incFile('fw/location/rus.php');
        
        /**
         * Подключение ключевых модулей
         */
        $db->query(SQL::get('sys_modules_core'));
        $this->includeModules($db->maps());
        
        /**
         * Подключение остальных модулей
         */
        $db->query(SQL::get('sys_modules_other'));
        $this->includeModules($db->maps());
        
        $args = args();
        if($args[0] == 'ajax') {
            $this->ajax = true;
            array_shift($args);
            $_GET['q'] = implode('/', $args);
        }
        
        /**
         * Запуск метода boot во всех модулях
         */
        $this->call('boot');
    }
    
    /**
     * Выполнение инициализации
     */
    public function init() {
        Page::getInstance();
        $this->call('init');
    }
    
    /**
     * Формирует список контроллеров и их callback-ов
     */
    private function controllers() {
        /**
         * Формирование контроллеров.
         * Пробегаем по всем объявленным модулям, собираем их контроллеры
         * с указанием их классов.
         */
        $controllers = [];
        foreach ($this->modules as $md){
            $cl = get_class($md);
            $cls = $md->controllers();
            foreach ($cls as $kk=>$vv) {
                $vv['class'] = $cl;
                $controllers[$kk] = $vv;
            }
        }
        return $controllers;
    }
    
    /**
     * Построение страницы
     */
    public function build() {
        $page = Page::getInstance();
        $this->call('build');
        $md = null;
        $r_url = url(); // Реальный url
        $urls = args();
        if(!$urls) {
            $urls[] = 'front';
            $r_url = 'front';
        }
        //--- Поиск в БД

        $res = Cache::getdb($r_url, 'url');
        if(count($res)>0) {
            $md = unserialize($res[0]['data']);
            $this->ctrlUrl = $r_url;
        } else {
            $controllers = $this->controllers();
            $ctrls = array_keys($controllers);

            /**
             * Поиск по всем контроллерам всех модулей
             */
            while($urls) {
                $url = implode('/', $urls);
                if(in_array($url, $ctrls)) {
                    $map = $controllers[$url];
                    $md = [
                        'cl'=>$map['class'],
                        'pr'=>isset($map['perms'])?$map['perms']:[],
                        'cb'=>$map['callback'],
                        'args'=>isset($map['args'])?$map['args']:[],
                        'title'=>isset($map['title'])?$map['title']:''
                        ];
                    $this->ctrlUrl = $url;
                    break;
                }
                array_pop($urls);
            }
            if($md) {
                Cache::setdb($url, serialize($md), 'url');
            }
        }
        
        $user = User::getInstance();
        if(isset($md['title'])) $page->setTitle($md['title']);
        $chkey = 'page_'.str_replace('/', '.', url());
        $html = false;
        $content = '';
        if(Cache::config('cfg.ccache.agress') && args(0) != 'admin') {
            $html = Cache::get($chkey);
        }

        if(!$html) {
            if($md != null) {
                if($user->checkAccess($md['pr'])) {
                    if(args(0) == 'admin' && !$user->checkPerm('admin panel')) g2p ('/');

                    $cb = $md['cb'];
                    $args = (isset($md['args'])?$md['args']:[]);
                    $md = (new $md['cl']());

                    $params = [];
                    $tmp = args();
                    $curls = count($urls);

                    if($args) {
                        $params = $args;
                        if($tmp) $params[] = array_slice($tmp, $curls);
                    } else {
                        if(count($tmp) > $curls) $params = array_slice($tmp, $curls);
                    }

                    $content = call_user_func_array([$md, $cb], $params);
                    $page->setData('content', $content);
                } else {
                    Message::add('Нет доступа', Message::MESSAGE_ERROR);
                    Message::save();
                    g2p('/');
                }
            } else {
                if(args(0) == 'admin' && !$user->checkPerm('admin panel')) g2p ('/');
                else {
                    $page->setData('content', '');
                    $page->setTitle('Accent');
                }
            }
            
//          ob_start('ob_gzhandler');
            $html = (!$this->isAJAX())?$page->view():$content;
            
            if(Cache::config('cfg.ccache.agress') && args(0) != 'admin') {
                Cache::set($chkey, $html);
            }
            
//          ob_end_flush();
            /**
             * Если страница не найдена
             */

            /** */
            if($md == null) {}
            
        }
        
//        ob_start('ob_gzhandler');
        print $html;
//        ob_end_flush();
    }
    
    private function call($func) {
        foreach ($this->modules as $md) $md->$func();
    }

    /**
     * Локализация
     * 
     * @param Array $lo
     */
    public function location($lo) {
        $this->location = array_merge($this->location, $lo);
    }
    
    /**
     * Переводчик
     * 
     * @param String $key
     * @return String
     */
    public function t($key) {
        return (isset($this->location[$key]))?$this->location[$key]:$key;
    }

    /**
     * Подключение модулей
     * @param Array $mods
     */
    private function includeModules($mods) {
        foreach ($mods as $id=>$rw) {
            $module = 'Module_'.$rw['name'];
            incFile('fw/modules/'.$rw['type'].'/'.$rw['name'].'/'.$module.'.php');
            $this->modules[] = new $module();
        }
    }
    
    public function path2files() { return 'files/all'; }

    public function controllerUrl() { return $this->ctrlUrl; }
}
