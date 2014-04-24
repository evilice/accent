<?php
/**
 * User: goncharovsv
 * Date: 22.03.13
 * Time: 11:49
 */
class Observer {
    private function __construct() {}
    private function __clone() {}

    private static $listeners = [];

    /**
     * Добавляет слушателя
     *
     * @param $class
     * @param $method
     * @param $callback
     */
    public static function addListener($class, $event, $callback) {
        if(!isset(self::$listeners[$class])) self::$listeners[$class] = [];
        if(!isset(self::$listeners[$class][$event])) self::$listeners[$class][$event] = [];
        self::$listeners[$class][$event][] = $callback;
    }

    /**
     * Вызов событий
     *
     * @param $event
     * @param bool $data
     */
    public static function callEvent(&$data = false) {
        if(self::$listeners) {
            $e = debug_backtrace()[1];
            if(isset(self::$listeners[$e['class']]) && isset(self::$listeners[$e['class']][$e['function']])) {
                $methods = self::$listeners[$e['class']][$e['function']];
                foreach($methods as $m) $m($data);
            }
        }
    }
}
