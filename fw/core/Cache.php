<?php
/**
 * Система кеширования
 * 
 * @author SP
 */
class Cache {
    /**
     * Настройки кеширования
     * @var type 
     */
    private static $config = [];

    /**
     * Путь к директории с кэш-файлами
     * @var String 
     */
    private static $p = 'fw/cache/';
    
    /**
     * Имя таблицы в БД с кэш-записями
     * @var String
     */
    private static $t = 'cache';
    
    /**
     * Кеш храняшийся в оперативной памяти
     * @var Map 
     */
    private static $ram = [];

    /**
     * Установка значений конфигурации
     * 
     * @param String $key
     * @param Object $value
     */
    public static function setConfig($key, $value) {
        self::$config[$key] = $value;
    }
    
    /**
     * Значение опции
     * 
     * @param String $key
     * @return Object
     */
    public function getConfig($key) {
        return self::$config['$key'];
    }
    
    /**
     * Возвращает из кэша объект по ключу.
     * Работа с файлами.
     * 
     * @var Object 
     */
    public static function get($key) {
        $cn = fileRead(self::$p.$key.'.cch');
        return ($cn != '')?unserialize($cn):false;
    }
    
    /**
     * Сохраняет объект в кэш по ключу.
     * Работа с файлами.
     *
     * @param String $key
     * @param Object/String $data
     */
    public static function set($key, $data) {
        fileWrite(self::$p.$key.'.cch', serialize($data));
    }
    
    /**
     * Отчищает кэш по ключу.
     * Если ключ не задан, отчищает весь кэш.
     * Работа с файлами.
     * 
     * @param String $key 
     */
    public static function clear($key = false) {
        if($key) fileDel (self::$p.$key.'.cch');
        else fileDelFromDir (self::$p);
    }
    
    /**
     * Поиск по ключу и типу в БД.
     * @todo Использовать map() вместо maps()
     * @param String $key
     * @param String $type
     * @return Array
     */
    public static function getdb ($key, $type = false) {
        $c = new SQLConstructor();
        
        $cond = ['@and'=>[
            ['cache_key'=>$key],
            ['type'=>$type],
        ]];
        return $c->find('cache', $cond)->maps();
    }
    
    /**
     * Сохранение в БД.
     * 
     * @param String $key
     * @param String $data
     * @param String $type
     */
    public static function setdb($key, $data, $type = false) {
        $db = new SQLConstructor();
        $db->insert('cache', [[$key, $type, $data, time()]])->execute();
    }
    
    /**
     * Отчистка кеша в БД
     * 
     * @param type $type
     */
    public static function cleardb($type = false) {
        $c = new SQLConstructor();
        $c->delete('cache');
        if($type) $c->where (['type'=>$type]);
        $c->execute();
    }
    
    /**
     * Сохранение данных в оперативной памяти
     * 
     * @param type $key
     * @param type $data
     */
    public static function setram($key, $data) { self::$ram[$key] = $data; }
    
    /**
     * Выбрка данных из оперативной памяти
     * 
     * @param type $key
     * @return type
     */
    public static function getram($key) {
        return (isset(self::$ram[$key])?self::$ram[$key]:false);
    }

    public static function rams() { return self::$ram; }
    
    /**
     *  Проверка конфигурации
     * 
     * @param string $key
     * @return bool
     */
    public static function config($key) {
        return (isset(self::$config[$key]) && (int)self::$config[$key] == 1);
    }
    
    public static function debug() { return self::$ram; }
}