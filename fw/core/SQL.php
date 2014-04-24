<?php
/**
 * Работает со списками SQL-запросов.
 */
class SQL {
    private static $collection = array();
    
    private function __construct() {}

    /**
     * Добавляет запрос в общий список.
     * Возврвщает FALSE, если ключ уже занят.
     * 
     * @param String $key
     * @param String $query
     * @return boolean
     */
    public static function set($key, $query) {
        if(!isset(self::$collection[$key])) {
            self::$collection[$key] = $query;
            return true;
        }
        return false;
    }
    
    /**
     * Возвращает текст запроса по ключу.
     * Если ключ не найден, возвращает FALSE.
     * 
     * @param String $key
     * @param String $query
     * @return type
     */
    public static function get($key) {
        return 
            (isset(self::$collection[$key]))?
                self::$collection[$key]:false;
    }
    
    /**
     * Выполняет замену '?' на значения, указанные
     * в параметрах метода.
     * 
     * @param String $key
     * @return string
     */
    public static function fill() {
        $args = func_get_args();
        $key = array_shift($args);
        $q = self::get($key);
        if($q) {
            while(true) {
                $arg = array_shift($args);
                if($arg !== null)
                    $q = preg_replace('/[?]/', $arg, $q, 1);
                else break;
            }
        }
        return $q;
    }
}
