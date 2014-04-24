<?php
/**
 * Description of DocumentType
 *
 * @author goncharovsv
 */
class DocumentType {
    private $table = 'doc_types';
    private $type;

    public function __construct($type = false) {
        if($type) $this->type = $type;
    }
    
    /**
     * Список типов документов
     * @return Array
     */
    public function types() {
        return (new SQLConstructor())->find($this->table)->maps();
    }

    /**
     * Тип документа
     *
     * @param $id
     * @return Array
     */
    public function get($id){
        return (new SQLConstructor())->find($this->table, ['tid'=>(int)$id])->map();
    }   
    
    /**
     * Список полей типа документов
     * 
     * @return array
     */
    public function fields() {
        $fields = [];
        $c = new SQLConstructor();
        $c->find('doc_type_fields dt', ['type'=>$this->type]);
        $c->join('fields', 'f', ['@eqf'=>['dt.fid', 'f.fid']], 'left');
        $ifs = $c->sort('weight')->maps();

        foreach($ifs as $f) {
            $field = new $f['module']();
            $field->build($f);
            $fields[] = $field;
        }
        return $fields;
    }

    /**
     * Удаление типа документа
     *
     * @param $id
     */
    public function delete() {
        //--- Удаляем тип документа
        if((new SQLConstructor())->delete($this->table, ['name'=>$this->type])->execute()) {
            //--- Выборка всех полей связанных с удалённым типом документа
            $fields = $this->fields();
            $tables = [];
            foreach ($fields as $fl) {
                $f = $fl->fid();
                if(!in_array($f, ['title', 'body', 'adt', ''])) {
                    /**
                     * Если поле есть только у удаляемого типа,
                     * то ставим его таблицу в очередь на удаление и 
                     * удаляем запись о нём в таблице `fields`
                     */
                    $t = new SQLConstructor();
                    $t->find('doc_type_fields', ['fid'=>$f]);
                    if($t->count() == 1) {
                        (new SQLConstructor())->delete('fields', ['fid'=>$f])->execute();
                        $tables[] = $fl->fid();
                    }
                }
            }
            (new SQLConstructor())->delete('doc_type_fields', ['type'=>$this->type])->execute();
            (new DB("DROP TABLE IF EXISTS ".  implode(',', $tables)))->execute();
        }
        return false;
    }

    /**
     * Создание типа документа
     *
     * @param $type
     */
    public function add($type) {}

    /**
     * Сохранение изменений в типе документов
     *
     * @param $type
     */
    public function save($type) {}
}