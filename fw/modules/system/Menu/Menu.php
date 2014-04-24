<?php
/**
 * Description of Menu
 *
 * @author goncharovsv
 */
class Menu {
    static private $list = [];
    static private $tree = [];

    public function __construct() {
        if(!self::$list) {
            $c = new SQLConstructor();
            $c->find('dictionary', ['@like'=>['parent', 'menu%']])->sort('parent', 'weight');

            $list = $c->maps();
            foreach ($list as $m) {
                $url = '';
                $cd = str_replace('menu.', '', $m['code']);
                $ls = explode('.', $cd);
                if(array_shift($ls) == 'admin') $url .= '/admin/';
                $url .= implode ('/', $ls);
                $m['url'] = $url;

                self::$list[$m['code']] = $m;
            }
        }
    }
    
    public function items($code, $childs = false) {
        $res = [];
        foreach (self::$list as $k) {
            if($k['parent'] == $code) {
                $mp = $k;
                if($childs) {
                    $ch = $this->items($k['code']);
                    if($ch) $mp['childs'] = $ch;
                }
                if($mp) $res[] = $mp;
            }
        }
        return $res;
    }
    
    public function subitems($code) {}
    
    public function breadcrumbs() {
        $res = [];
        $ls = args();
        $el = true;
        while($ls && $el) {
            $cd = 'menu.'.(implode('.', $ls));
            if(isset(self::$list[$cd])) {
                $b = self::$list[$cd];
                $res[] = [
                    'title'=>$b['title'],
                    'url'=>$b['url']
                ];
            }
            $el = array_pop($ls);
        }
        return array_reverse($res);
    }
}