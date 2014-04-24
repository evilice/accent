<?php
/**
 * Description of Message
 *
 * @todo доделать систему сообщений. Сохранение, Удаление и вывод сообщений
 * @author SP
 */
class Message {
    const MESSAGE_ERROR = 'er';
    const MESSAGE_WARNING = 'wr';
    const MESSAGE_INFO = 'inf';
    
    private static $list = [];
    
    private function __construct() {}
    
    /**
     * Инициализация
     */
    public static function init() {
        //--- Поиск записей в БД по id сессии
    	$sid = session_id();
    	$db = new DB(SQL::fill('messages_get_sid', $sid));
    	$res = $db->map();

        //--- если записи найдены
    	if($res) {
            /**
             * Переносим записи в буфер и чистим БД
             */
            self::$list = unserialize($res['data']);
            $db = new DB(SQL::fill('messages_del_sid', $sid));
            $db->execute();
    	}
    }
    
    /**
     * Добавляет сообщение
     * 
     * @param string $text
     * @param string $type
     */
    public static function add($text, $type = self::MESSAGE_INFO) {
        if(!isset(self::$list[$type])) self::$list[$type] = [];
    	self::$list[$type][] = $text;
    }
    
    /**
     * Возвращает список сообщений
     * 
     * @return array
     */
    public static function get() {
    	$list = self::$list;
    	self::$list = [];
    	return $list;
    }
    
    /**
     * Сохраняет сообщения в БД
     */
    public static function save() {
    	if(self::$list) {
            $db = new DB(SQL::fill('messages_insert', session_id(), serialize(self::$list), time()));
            $db->execute();
    	}
    }
}