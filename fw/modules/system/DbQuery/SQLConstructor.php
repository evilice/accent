<?php
/**
 * Конструктор SQL запросов
 *
 * @author goncharovsv
 */

/**
 * Примеры
 * 
    $db = new SQLConstructor();
 * 
    $db->find('modules', array('package'=>'core'));
    select * from modules where package='core'

    $db->find('modules')->sort(array('gt'=>'name'));
    select * from modules order by name desc

    $db->find('modules')->fields('id', 'name');
    select id, name from modules

    $db->find('modules')->limit(0, 5);
    select * from modules limit 0, 5

    $db->findOne('modules');
    select * from modules limit 1

    $db->find('modules', array('@like'=>array('package', 'co%')));
    select * from modules where package like 'co%'
 */
class SQLConstructor {
    private $sql = '';
    private $tables = [];
    private $fields = [];
    private $values = [];
    private $joins = [];
    private $group = [];
    private $conditions = [];
    private $limit = '';
    private $sort = [];
    private $type = '';
    private $duplicate_update = [];
    private $connect = null;

    public function __construct($db = false) {
        if($db) $this->connect = $db;
    }

    /**
     * Поиск записей (подобие select)
     * 
     * @param String $table
     * @param Array $condition
     * @return \SQLConstructor
     */
    public function find($table, $condition = false) {
        $this->type = 'find';
        $this->tables[] = $table;
        if($condition) $this->conditions = $condition;
        return $this;
    }
    
    /**
     * Выборка
     */     
    public function select() {
        $this->type = 'select';
        $this->tables = array_merge($this->tables, func_get_args());
    }
    
    /**
     * Внесение изменений
     * 
     * @param String $table
     * @param Array $values
     * @param Array $condition
     * @param Array $fields
     * @return \SQLConstructor
     */
    public function update($table, $values, $condition = false) {
        $this->type = 'update';
        $this->tables($table);
        foreach ($values as $k=>$v) {
            if($k == '@flds') $this->values[] = $v[0]."=".$v[1];
            else $this->values[] = $k."=".$this->cv($v);
        }

        if($condition) $this->conditions = $condition;

        return $this;
    }
    
    /**
     * Вставка
     * 
     * @param String $table
     * @param Array $values
     * @return \SQLConstructor
     */
    public function insert($table, $values, $duplicate_update_values = []) {
        $this->type = 'insert';
        $this->tables($table);
        $this->values = $values;
        $this->duplicate_update = $duplicate_update_values;
        return $this;
    }
    
    /**
     * Удаление
     * 
     * @param type $table
     * @param type $condition
     * @return \SQLConstructor
     */
    public function delete($table, $condition=false) {
        $this->type = 'delete';
        $this->tables[] = $table;
        if($condition) $this->conditions = $condition;
        return $this;
    }
    
    /**
     * Установка таблиц, учавствующих в запросе
     * 
     * @return \SQLConstructor
     */
    public function tables() {
        $this->tables = array_merge($this->tables, func_get_args());
        return $this;
    }
    
    /**
     * 
     * @param type $table
     * @param type $alias
     * @return \SQLConstructor
     */
    public function table($table, $alias=false) {
        $this->tables[] = $table.($alias?' '.$alias:'');
        return $this;
    }
    
    /**
     * Поля, учавствующие в запросе
     * 
     * @return \SQLConstructor
     */
    public function fields() {
        $this->fields = array_merge($this->fields, func_get_args());
        return $this;
    }
    
    /**
     * Установка параметров фильтрации данных
     * 
     * @param Map $condition
     * @return \SQLConstructor
     */
    public function where($condition) {
        if($condition) $this->conditions = $condition;
        return $this;
    }
    
    /**
     * Установка параметров сортировки
     * 
     * @return \SQLConstructor
     */
    public function sort() {
        $this->sort = $this->sort + func_get_args();
        return $this;
    }
    
    /**
     * Лимит выходных строк
     * 
     * @param int $lt
     * @param int $gt
     * @return \SQLConstructor
     */
    public function limit($lt, $gt = false) {
        $this->limit = $lt.($gt?', '.$gt:'');
        return $this;
    }
    
    /**
     * Поля групперовки
     * @return \SQLConstructor
     */
    public function group() {
        $flds = func_get_args();
        foreach ($flds as $f) $this->group[] = $f;
        return $this;
    }
    
    /**
     * Выполнение JOIN-а
     * 
     * @param String $table
     * @param String $alias
     * @param Array $condition
     * @param String $type
     * @return \SQLConstructor
     */
    public function join($table, $alias, $condition, $type) {
        $this->joins[] = [
            'table'=>$table,
            'alias'=>$alias,
            'condition'=>$condition,
            'type'=>$type
        ];
        return $this;
    }
    
    /**
     * Генерация SQL кода
     * 
     * @return string
     */
    public function toString() {
        $str = "";
        switch ($this->type) {
            case 'find': {
                $str = "SELECT ";
                $str .= ($this->fields)?(implode(', ', $this->fields)):'*';
                $str .= " FROM ";
                $str .= implode(', ', $this->tables).' ';
                
                $str .= $this->_join();
                
                $cnd = $this->_cond($this->conditions);
                if($cnd != "") $str .= ' WHERE '.$cnd;
                
                $srt = $this->_sort();
                if($srt != "") $str .= " ORDER BY ".$srt;
                
                if($this->group) $str .= " GROUP BY ".$this->_group();
                
                if($this->limit != "") $str .= " LIMIT ".$this->limit;
                
                break;
            }
            case 'insert': {
                $str = "INSERT INTO ";
                $str .= implode(', ', $this->tables).' ';
                $str .= ($this->fields)?"(".implode(", ", $this->fields).")":"";
                
                $vals = [];
                foreach ($this->values as $v) $vals[] = "(".$this->cv($v).")";
                $str .= "VALUES".  implode(',', $vals);
                if(count($this->duplicate_update) > 0){
                    $str .= " ON DUPLICATE KEY UPDATE ";
                    array_walk($this->duplicate_update, function(&$item){
                        $item = "$item=VALUES($item)";
                    });
                    
                    $str .= implode(', ', $this->duplicate_update);
                    $this->duplicate_update = [];
                }
                break;
            }
            case 'update': {
                $str = "UPDATE ".(implode(', ', $this->tables).' ');
                $str .= "SET ".implode(", ", $this->values)." ";
                
                $cnd = $this->_cond($this->conditions);
                if($cnd != "") $str .= "WHERE ".$cnd;
                break;
            }
            case 'delete': {
                $str = "DELETE FROM ".(implode(', ', $this->tables).' ');
                $cnd = $this->_cond($this->conditions);
                if($cnd != "") $str .= " WHERE ".$cnd;
                break;
            }
        }
        
        $this->sql = $str;
        return $str;
    }
    
    /**
     * Построение условия запроса
     * 
     * @return String
     */
    private function _cond($conds) {
        $str = "";
        foreach ($conds as $k => $v) {
            switch($k) {
                case '@noteq': { $str .= $v[0]." != ".$this->cv($v[1]); break; }
                case '@notlike': { $str .= $v[0]." not like ".$this->cv($v[1]); break; }
                case '@like': { $str .= $v[0]." like ".$this->cv($v[1]); break; }
                case '@in': { $str .= $v[0]." in (".$this->cv($v[1]).")"; break; }
                case '@notin': { $str .= $v[0]." not in (".$this->cv($v[1]).")"; break; }
                case '@and': {
                    $tmp = [];
                    foreach ($v as $t) $tmp[] = $this->_cond($t);
                    $str .= implode(" AND ", $tmp); 
//                    $str .= $this->_cond($v[0])." AND ".$this->_cond($v[1]); 
                    break;
                }
                case '@or': { $str .= $this->_cond($v[0])." OR ".$this->_cond($v[1]); break; }
                case '@gt': { $str .= $v[0].'>'.$v[1]; break; }
                case '@lt': { $str .= $v[0].'<'.$v[1]; break; }
                case '@eqf': { $str .= $v[0].'='.$v[1]; break; } //--- Сравнение полей
                default : { $str .= ($str!=''?' AND ':'').$k."=".$this->cv($v); }
            }
        }
        return $str;
    }
    
    /**
     * Парсинг сортировки
     * 
     * @return string
     */
    private function _sort() {
        $st = array();
        foreach ($this->sort as $k=>$v) {
            if(gettype($v) == 'array') {
                if(isset($v['gt'])) $st[] = $v['gt']." DESC";
                else $st[] = $v['lt']." ASC";
            } else $st[] = $v;
        }
        return implode(', ', $st);
    }

    /**
     * Генерация group by
     * 
     * @return String
     */
    private function _group() { return implode(', ', $this->group); }
    
    /**
     * Проверка типа значения
     * 
     * @param undefined $val
     * @return String
     */
    private function cv($val) {
        $type = gettype($val);
        if(in_array($type, array('bool', 'integer', 'double', 'float'))) {
            return $val;
        } elseif($type == 'array') {
            $tm = array();
            foreach ($val as $kk=>$vv) $tm[] = $this->cv($vv);
            return implode(', ', $tm);
        } else {
            return "'".$this->s($val)."'";
        }
    }
    
    private function _join() {
        $str = "";
        if($this->joins) {
            foreach ($this->joins as $k=>$j) {
                $str .= $j['type']." join ";
                $str .= $j['table'].' '.$j['alias'].' ';
                $str .= 'on '.$this->_cond($j['condition']) . ' ';
            }
        }
        return $str;
    }
    
    /**
     * Приведение строки к безопасному виду.
     * Экранирование спец символов
     * @param type $str
     * @return type
     */
    private function s($str) {return mysql_real_escape_string($str); }
    
    /**
     * Перебросы на системный класс для работы с БД
     * @return \DB
     */
    public function db() {
        if(!$this->connect) return new DB($this->toString());
        else {
            $this->connect->query($this->toString());
            return $this->connect;
        }
    }
    public function maps($key = false) { return $this->db()->maps($key); }
    public function map() { return $this->db()->map(); }
    public function objects() { return $this->db()->objects(); }
    public function object() { return $this->db()->object(); }
    public function execute() { return $this->db()->execute(); }
    public function lastId() { return mysql_insert_id(); }
    public function count() { return $this->db()->count(); }
}