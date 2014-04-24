<?php
class SEO {
    private function __construct() {}
    private function __clone() {}

    public static function getTopWords($str, $count = 20, $minLetters = 6) {
        $res = '';
        $hash = md5(url());
        $cache = Cache::getdb($hash, 'seo');
        if(!$cache) {
            $counters = [];
            $str = strip_tags($str);
            $words = explode(' ', str_replace(str_split('[](),.-!?;:+@#$%^&*\'"\n\r\t'), '', $str));
            foreach($words as $w) {
                if(mb_strlen($w, 'utf-8') > $minLetters) {
                    if(!isset($counters[$w])) $counters[$w] = 1;
                    else $counters[$w]++;
                }
            }
            arsort($counters);
            $i = 0;
            foreach($counters as $k=>$v) {
                if($i++ == $count) break;
                $res[$k] = $v;
            }
            if($res && array_keys($res)) {
                $res = implode(', ', array_keys($res));
                Cache::setdb($hash, $res, 'seo');
            }
        } else $res = $cache[0]['data'];
        return $res;
    }
}