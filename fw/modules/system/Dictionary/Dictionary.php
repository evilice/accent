<?php
/**
 * Словарь
 *
 * @author goncharovsv
 */
class   Dictionary {
    private $code = '';
    private $val;
    private $title;
    private $parent;
    private $weight = 0;
    
    /*************************
     * Геттеры и сеттеры
     *************************/
    public function getCode() { return $this->code; } 
    public function getVal() { return $this->value; } 
    public function getTitle() { return $this->title; } 
    public function getParent() { return $this->parent; } 
    public function getWeight() { return $this->weight; } 
    public function setCode($x) { $this->code = $x; } 
    public function setVal($x) { $this->val = $x; } 
    public function setTitle($x) { $this->title = $x; } 
    public function setParent($x) { $this->parent = $x; } 
    public function setWeight($x) { $this->weight = $x; }
    
    /************************
     * Основной функционал
     ************************/
    
    /**
     * Установка параметров
     * 
     * @param Array $data
     */
    public function setData($data) {
        foreach ($data as $k=>$v)
            $this->$k = $v;
    }
    
    /**
     * Создание узла
     */
    public function add() {
        $c = new SQLConstructor();
        $c->insert('dictionary', [[$this->code, $this->parent, $this->title, $this->val, $this->weight]]);
        $c->fields('code', 'parent', 'title', 'val', 'weight');
        $c->execute();
    }
    
    /**
     * Возвращает узел по его коду
     * 
     * @return Array
     */
    public function get() {
        $c = new SQLConstructor();
        $c->find('dictionary', ['code'=>$this->code]);
        return $c->map();
    }
    
    /**
     * Сохраняет изменения
     * 
     * @return boolean
     */
    public function save() {
        $c = new SQLConstructor();
        $c->update('dictionary', ['title'=>$this->title, 'val'=>$this->val], ['code'=>$this->code]);
        return ($c->execute())?true:false;
    }
    
    /**
     * Удаление узла и его потомков
     * 
     * @return Mysql_Result
     */
    public function delete() {
        $c = $this->code;
        $db = new DB(SQL::fill('dictionary_delete', $c, $c, $c));
        return $db->execute();
    }
    
    /**
     * Возвращет список потомков
     * 
     * @return Array
     */
    public function childs($childs = false) {
        $res = [];
        if($childs) {
            $db = new DB(SQL::fill('dictionary_with_childs', $this->code, $this->code));
            $list = $db->maps();
            foreach ($list as $r) {
                if(isset($res[$r['parent']])) {
                    if(isset($res[$r['parent']]['childs'])) $res[$r['parent']]['childs'][] = $r;
                    else $res[$r['parent']]['childs'] = [$r];
                } else $res[$r['code']] = $r;
            }
        } else {
            $db = new DB(SQL::fill('dictionary_childs', $this->code));
            $res = $db->maps();
        }
        return $res;
    }
    
    /**
     * Построение дерева
     * 
     * @param string $code
     * @return array
     */
    public function tree($code = false) {
        $code = (isset($code)?$code:$this->code);
        
        $c = new SQLConstructor();
        $list = $c->find('dictionary', ['parent'=>$code])->sort('weight')->maps('code');
        foreach ($list as $k=>$v) {
            $ch = $this->tree($k); //--- поиск потомков
            if($ch) $list[$k]['childs'] = $ch;
        }
        
        return $list;
    }

    /**
     * Возвращает ассоциативный массив словаря
     *
     * @param $parent
     * @return array
     */
    public function map($parent) {
        $res = [];
        $d = new SQLConstructor();
        $list = $d->find('dictionary', ['parent'=>$parent])->sort('weight')->maps();
        foreach($list as $l) {
            $tmp = explode('.', $l['code']);
            array_shift($tmp);
            $res[implode('.', $tmp)] = $l;
        }
        return $res;
    }
}