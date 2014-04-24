<?php
/**
 * Description of FieldSelect
 *
 * @author SP
 */
class FieldSelect extends FormField {
    private $options = []; //--- Список опций
    private $selected = false;

    public function value($value = false) {
        if($value) $this->selected = $value;
        else return $this->selected;
    }
    
    /**
     * Добавляет опцию в селект
     * 
     * @param string $value
     * @param string $text
     */
    public function option($text, $value = false, $selected = false) {
        $this->options[] = [
            'text'=>$text,
            'value'=>$value,
            'selected'=>($selected)?true:false
        ];
    }
    
    public function createTable($fid) {
        $sql = "
            CREATE TABLE ".$fid."(
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `doc` INT(11) NOT NULL,
                `val` VARCHAR(100),
                PRIMARY KEY (`id`),
                INDEX doc (`doc`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $db = new DB($sql);
        return ($db->execute())?true:false;
    }
    
    public function build($data) {
        $conf = unserialize($data['config']);
        $list = [];
        $build = function($ls, $level = 0) use(&$list, &$build) {
            foreach($ls as $l) {
                $list[] = ['title'=>str_repeat('- ', $level).$l['title'], 'code'=>$l['code']];
                if(isset($l['childs'])) $build($l['childs'], $level+1);
            }
        };
        $build((new Dictionary())->tree($conf['keydictionary']));

        foreach($list as $el) $this->option($el['title'], $el['code']);

        $this->title($data['title']);
        $this->name($data['fid']);
        $this->fid($data['fid']);
        if(isset($data['value'])) $this->value ($data['value']);
    }
    
    public function config() {
        $ch = new FieldCheckbox('Взять значения из справочника', ['name'=>'fromDictionary']);
        $code = new FieldText('Код справочника', ['name'=>'keydictionary']);
        
        return [$ch, $code];
    }
    
    public function info() {
        return [
            'title'=>'Выпадающий список',
            'description'=>''
        ];
    }

    public static function resource($fid, $data) {
        if(!$data) return '';
        $data = $data[0];
        $c = new SQLConstructor();
        $els = $c->find($fid, ['doc'=>$data['doc']])->fields('val')->maps();
        return $els[0]['val'];
    }

    public static function listners() {
        $saveResult = function(&$doc) {
            $fields = $doc->fieldsByModule();
            if(isset($fields[__CLASS__])) {
                foreach($fields[__CLASS__] as $fid=>$data) {
                    if((new SQLConstructor())->find($fid, ['doc'=>$doc->getId()])->count() > 0)
                        (new SQLConstructor())->update($fid, ['val'=>$data['value']])->where(['doc'=>$doc->getId()])->execute();
                    else (new SQLConstructor())->insert($fid, [[$doc->getId(), $data['value']]])->fields('doc', 'val')->execute();
                }
            }
        };
        Observer::addListener('Document', 'add', function(&$doc) use ($saveResult) { $saveResult($doc); });
        Observer::addListener('Document', 'save', function(&$doc) use ($saveResult) { $saveResult($doc); });
        Observer::addListener('Document', 'del', function(&$doc) {
            $fields = $doc->fieldsByModule();
            if(isset($fields[__CLASS__])) {
                foreach($fields[__CLASS__] as $fid=>$data) {
                    (new SQLConstructor())->delete($fid, ['doc'=>$doc->getId()])->execute();
                }
            }
        });
    }
    
    /**
     * Устанавливает или возвращает опции элемента селект
     * 
     * @param array $data
     * @return array
     */
    public function options($data = false) {
        if($data) $this->options = $data;
        else return $this->options;
    }
}