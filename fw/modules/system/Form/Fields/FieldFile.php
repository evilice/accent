<?php
/**
 * Description of FieldFile
 *
 * @author SP
 */

class FieldFile extends FormField {
    public function __construct($title = false, $attributes = false) {
        parent::__construct($title, $attributes);
        $page = Page::getInstance();
        $page->js(pathToModule('Form').'/js/FieldFile.js');
        $page->css('/'.pathToModule('Form').'/css/FieldFile.css');
    }

    public function config() {
        $min = new FieldText('Минимальное количество значений', ['name'=>'size']);
        $max = new FieldText('Максимальное количество значений', ['name'=>'size']);
        return [$min, $max];
    }
    
    public function createTable($fid) {
        $sql = "
            CREATE TABLE ".$fid."(
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `doc` INT(11) NOT NULL,
                `title` TEXT,
                `path` VARCHAR(255),
                PRIMARY KEY (`id`),
                INDEX doc (`doc`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $db = new DB($sql);
        return ($db->execute())?true:false;
    }

    /**
     * Перехват событий
     */
    public static function listners() {
        $saveData = function(&$doc) {
            $docId = $doc->getId();
            $fields = $doc->fieldsByModule();
            $sys = System::getInstance();

            if(isset($fields[__CLASS__])) {
                foreach($fields[__CLASS__] as $fid=>$data) {
                    $config = unserialize($data['config']);

                    $p = $sys->path2files().'/'.$fid.'/'.implode('/', str_split($docId, 2));
                    if(!is_dir($p)) mkdir($p, 0755, true);

                    $insdt = [];
                    $dv = $data['value'];
                    if(gettype($dv['name'])=='array') {
                        $cnt = count($dv['name']);
                        for($i=0; $i<$cnt; $i++) {
                            $newName = substr(randstr(), -7);
                            $tmp = explode('.', $dv['name'][$i]);
                            $ext = array_pop($tmp);
                            $nm = implode('.', $tmp);

                            if(self::_action($dv['tmp_name'][$i], $p.'/'.$newName.'.'.$ext, $config))
                                $insdt[] = [$docId, $nm, $newName.'.'.$ext];
                        }
                    } else {
                        $pth = $p.'/'.$dv['name'];
                        if(self::_action($dv['tmp_name'], $pth, $config))
                            $insdt[] = [$docId, '', $dv['name']];
                    }

                    (new SQLConstructor())->insert($fid, $insdt)->fields('doc', 'title', 'path')->execute();
                }
            }
        };

        Observer::addListener('Document', 'add', function(&$doc) use ($saveData) { $saveData($doc); });
        Observer::addListener('Document', 'save', function(&$doc) use ($saveData) { $saveData($doc); });
        Observer::addListener('Document', 'del', function(&$doc) {
            $fields = $doc->fieldsByModule();
            if(isset($fields[__CLASS__])) {
                foreach($fields[__CLASS__] as $fid=>$data) {
                    $sys = System::getInstance();
                    $path = $sys->path2files().'/'.$fid.'/'.implode('/', str_split($doc->getId(), 2));
                    $files = scandir($path);
                    foreach($files as $f) {
                        if(is_file($path.'/'.$f)) unlink($path.'/'.$f);
                    }
                    rmdir($path);
                    (new SQLConstructor())->delete($fid, ['doc'=>$doc->getId()])->execute();
                }
            }
        });
    }
    
    public static function resource($fid, $data) {
        $res = [];
        $sys = System::getInstance();
        
        foreach($data as $d) {
            $d['original'] = $sys->path2files().'/'.$fid.'/'.implode('/', str_split($d['doc'], 2)).'/'.$d['path'];
            $res[] = $d;
        }
        return $res;
    }
    
    private static function _action($name, $newname, $config) {
        return ($name)?rename($name, $newname):false;
    }

    public static function deleteValues($fid, $ids) {
        $sys = System::getInstance();
        if(!$fid) return false;
        if(gettype($ids) != 'array') $ids = [$ids];

        $files = (new SQLConstructor())->find($fid, ['@in'=>['id', $ids]])->maps();
        foreach($files as $file) {
            $path = $sys->path2files().'/'.$fid.'/'.implode('/', str_split($file['doc'], 2));
            unlink($path.'/'.$file['path']);
        }
        (new SQLConstructor())->delete($fid, ['@in'=>['id', $ids]])->execute();
    }

//    public function delete($docID) {
//        $sys = System::getInstance();
//        $path = $sys->path2files().'/'.$this->fid.'/'.implode('/', str_split($docID, 2));
//        $files = scandir($path);
//        foreach($files as $f) {
//            if(is_file($path.'/'.$f)) unlink($path.'/'.$f);
//        }
//        rmdir($path);
//        (new SQLConstructor())->delete($this->fid, ['doc'=>$docID])->execute();
//    }
    
    public function info() {
        return [
            'title'=>'Файл',
            'description'=>''
        ];
    }
}