<?php
/**
 * Description of FieldText
 *
 * @author goncharovsv
 */
class FieldText extends FormField {
    
    public function createTable($fid) {
        $sql = "
            CREATE TABLE ".$fid."(
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `doc` INT(11) NOT NULL,
                `text` TEXT,
                PRIMARY KEY (`id`),
                INDEX doc (`doc`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $db = new DB($sql);
        return ($db->execute())?true:false;
    }
    
    public function config() {
        $maxLength = new FieldText(false, ['name'=>'maxLenght']);
        $maxLength->upTitle('Максимальная длина строки');
        $minLength = new FieldText(false, ['name'=>'minLenght']);
        $minLength->upTitle('Минимальная длина строки');
        
        return [new FieldGroup([$minLength, $maxLength])];
    }
    
    public function info() {
        return [
            'title'=>'Однострочное текстовое поле',
            'description'=>''
        ];
    }

    public static function listners() {
        $saveResult = function(&$doc) {
            $fields = $doc->fieldsByModule();
            if(isset($fields[__CLASS__])) {
                foreach($fields[__CLASS__] as $fid=>$data) {
                    if((new SQLConstructor())->find($fid, ['doc'=>$doc->getId()])->count() > 0)
                        (new SQLConstructor())->update($fid, ['text'=>$data['value']])->where(['doc'=>$doc->getId()])->execute();
                    else (new SQLConstructor())->insert($fid, [[$doc->getId(), $data['value']]])->fields('doc', 'text')->execute();
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
}