<?php

class Debug {
    private $time = 0;
    private static $counter = [];
    
    public function memory() { return memory_get_usage(); }
    public function time_start() { $this->time = microtime(); }
    public function time_stop() { return (microtime() - $this->time); }
    
    /**
     * 
     * @param string $key
     */
    public static function counter($key) {
        if(!isset(self::$counter[$key])) self::$counter[$key] = 0;
        self::$counter[$key]++;
    }
    
    /**
     * 
     * @param string $key
     * @return int
     */
    public static function getCounter($key) { return self::$counter[$key]; }
}