<?php
/**
 * Работа с БД.
 *
 * @author goncharovsv
 */
class DB {
    private $query = null;
    private static $connect = null;
    private $oc = null; //open connect
    
    public function __construct($query = null) {
        $this->query = $query;
        $this->oc = self::$connect;
    }
    
    /**
     * Создание подключения к БД MySQL
     * 
     * @return boolean
     */
    public function connect() {
        self::$connect = mysql_connect(DB_CONNECT_HOST, DB_CONNECT_USER, DB_CONNECT_PASS);
        if(self::$connect) {
            $this->oc = self::$connect;
            if(mysql_select_db(DB_CONNECT_DBNAME, self::$connect)) {
                mysql_query("set names utf8");
                return true;
            }
        }
        return false;
    }

    public function openConnect($host, $db, $user, $pass) {
        $this->oc = mysql_connect($host, $user, $pass);
        if($this->oc) {
            if(mysql_select_db($db, $this->oc)) {
                mysql_query("set names utf8");
                return true;
            }
        }
        return false;
    }
    
    /**
     * Меняет тескт запроса на новый
     * 
     * @param String $query
     */
    public function query($query) { $this->query = $query; }
    
    /**
     * Возвращает объект указанного класса
     * 
     * @param String|Object $className
     * @return Object
     * 
     * @param String $className
     * @return Object
     */
    public function object($className = 'stdClass') {
        $obj = null;
        if($res = $this->execute()) 
            $obj = mysql_fetch_object($res);
        return $obj;
    }
    
    /**
     * Возвращает список объектов указанног класса
     * 
     * @param String|Object $className
     * @return Array
     */
    public function objects($className = 'stdClass') {
        $list = [];
        if($res = $this->execute()) {
            while($obj = mysql_fetch_object($res, $className)) {
                $list[] = $obj;
            }
        }
        return $list;
    }
    
    /**
     * Возвращает ассоциативный массив
     * 
     * @return Array
     */
    public function map() {
        $map = [];
        $cache = Cache::getram($this->query);
        if(!$cache) {
            if($res = $this->execute()) {
                $map =  mysql_fetch_assoc($res);
                Cache::setram($this->query, $map);
            } else return null;
        } else $map = $cache;

        return $map;
    }
    
    /**
     * Возврящает список ассоциативных массивов
     * 
     * @return Array
     */
    public function maps($key = false) {
        $list = [];
        $cache = Cache::getram($this->query);
        if(!$cache) {
            if($res = $this->execute()) {
                while ($row = mysql_fetch_assoc($res)) {
                    if($key) $list[$row[$key]] = $row;
                    else $list[] = $row;
                }
            }
            Cache::setram($this->query, $list);
        } else $list = $cache;
        return $list;
    }
    
    /**
     * Возвращает id последней добавленной записи
     * 
     * @return int
     */
    public function lastIndex() { return mysql_insert_id($this->oc); }
    
    /**
     * Количество найденных записей
     * 
     * @return int
     */
    public function count() {
        $res = $this->execute();
        return mysql_num_rows($res);
    }
    
    /**
     * Выполнение запроса
     * 
     * @return resource
     */
    public function execute() {
        Debug::counter('db');
        return mysql_query($this->query, $this->oc);
    }
    
    /**
     * Количество затронутых запросом строк
     * @return Integer
     */
    public function rowsChanged() { return mysql_num_rows($this->oc); }
    
    /**
     * 
     * @return String
     */
    public function error() { return mysql_error($this->oc); }
    
    public function debug() { return $this->query; }
    
    private function cache() {
        $res = Cache::getram($this->query);
        if(!$res) {
            $res = mysql_query($this->query, $this->oc);
            Cache::setram($this->query, $res);
        }
        Debug::counter('db');
    }
}