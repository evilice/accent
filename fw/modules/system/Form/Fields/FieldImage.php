<?php
/**
 * Description of FieldImage
 *
 * @author SP
 */
class FieldImage extends FormField {
    private $multiple = false; // мульти загрузка
    private $min = 0; // минимальное кол-во файлов
    private $max = 0; // максимальное кол-во файлов
    protected $fid;

    public function __construct($title = false, $attributes = false) {
        $page = Page::getInstance();
        parent::__construct($title, $attributes);
        $page->js(pathToModule('Form').'/js/FieldImage.js');
        $page->css('/'.pathToModule('Form').'/css/FieldImage.css');
        $this->attrs(['class'=>__CLASS__]);
    }

    public function config() {
        $original = new FieldCheckbox('Сохранять оригинал', ['name'=>'saveOriginal']);
        $original->checked(true);
        $title = new FieldCheckbox('Отображать поле "Описание"', ['name'=>'imgTitle']);
        $title->checked(true);

        $size = new FieldText('Максимально возможное количество значений', ['name'=>'size']);
        $default = new FieldImage('Значение по умолчанию', ['name'=>'defaultValue']);

        $dimension = new FieldText('Размеры изображений', ['name'=>'dimension']);
        $dimension->description('например: 960x480, 320x125, 120x90');

        return [$original, $title, $size, $default, $dimension];
    }

    public function createTable($fid) {
        $sql = "
            CREATE TABLE ".$fid."(
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `doc` INT(11) NOT NULL,
                `original` VARCHAR(255),
                `title` VARCHAR(255),
                `main` INT(2) NOT NULL DEFAULT 0,
                `weight` INT(11) DEFAULT 0,
                PRIMARY KEY (`id`),
                INDEX doc (`doc`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $db = new DB($sql);
        return ($db->execute())?true:false;
    }

    /**
     * Путь до файла с учётом id документа
     *
     * @param $fid
     * @param $doc
     * @return string
     */
    private static function pathToFile($fid, $doc) {
        $sys = System::getInstance();
        return $sys->path2files().'/'.$fid.'/'.implode('/', str_split($doc, 2));
    }

    /**
     * Перехват событий
     */
    public static function listners() {
        $saveData = function(&$doc) {
            $fds = $doc->fields();
            $fields = $doc->fieldsByModule();
            if(isset($fields[__CLASS__])) {
                foreach($fields[__CLASS__] as $fid=>$data) {
                    $config = unserialize($data['config']);
                    $docId = $doc->getId();
                    $p = self::pathToFile($fid, $docId);
                    if(!is_dir($p)) mkdir($p, 0755, true);

                    $insdt = [];
                    $dv = $data['value'];
                    if(count($dv['name'])>=1 && $dv['name'][0]!='') {
                        if(gettype($dv['name'])=='array') {
                            $cnt = count($dv['name']);
                            if($cnt == 0) return true;
                            $sz = (int)$config['size'];
                            $sz = ($sz>0)?$sz:$cnt;

                            for($i=0; $i<$sz; $i++) {
                                $pth = $p.'/'.tl($dv['name'][$i]);
                                if(file_exists($pth)) {
                                    $ft = pathinfo($pth);
                                    $pth = $p.'/'.randstr().'.'.$ft['extention'];
                                }
                                if($dv['tmp_name'] && !file_exists($p)) mkdir($p, 0755, true);
                                if(self::_action($dv['tmp_name'][$i], $pth, $config['dimension']))
                                    $insdt[] = [$docId, tl($dv['name'][$i]), $fds['_fieldImageNewTitle'][$i]];
                            }
                        } else {
                            $pth = $p.'/'.tl($dv['name']);
                            if($dv['tmp_name'] && !is_dir($p)) mkdir($p, 0755, true);
                            if(self::_action($dv['tmp_name'], $pth, $config['dimension']))
                                $insdt[] = [$docId, tl($dv['name'])];
                        }
                    }
                    (new SQLConstructor())->insert($fid, $insdt)->fields('doc', 'original', 'title')->execute();
                    if(isset($fds['_'.$fid])) {
                        foreach($fds['_'.$fid] as $k=>$v) {
                            list($t, $d) = explode('_', $k);
                            (new SQLConstructor())->update($fid, ['title'=>$v], ['id'=>(int)$d])->execute();
                        }
                    }
                }
            }
        };

        //--- Создание документа
        Observer::addListener('Document', 'add', function(&$doc) use ($saveData) { $saveData($doc); });

        //--- Сохранение документа
        Observer::addListener('Document', 'save', function(&$doc) use ($saveData) { $saveData($doc); });

        //--- Удаление документа
        Observer::addListener('Document', 'del', function(&$doc) {
            $fields = $doc->fieldsByModule();
            if(isset($fields[__CLASS__])) {
                foreach($fields[__CLASS__] as $fid=>$data) {
                    $ids = [];
                    $imgs = (new SQLConstructor())->find($fid, ['doc'=>$doc->getId()])->fields('id')->maps();
                    foreach($imgs as $i) $ids[] = $i['id'];
                    self::deleteValues($fid, $ids);
                    rmdir(self::pathToFile($fid, $doc->getId()));
                }
            }
        });
    }

    /**
     * Удаление данных
     *
     * @param $fid
     * @param $ids
     * @return bool|void
     */
    public static function deleteValues($fid,  $ids) {
        if(!$fid) return false;
        $config = unserialize((new SQLConstructor())->find('fields', ['fid'=>$fid])->map()['config']);
        if(gettype($ids) != 'array') $ids = [$ids];
        $imgs = (new SQLConstructor())->find($fid, ['@in'=>['id', $ids]])->maps();
        foreach($imgs as $img) {
            $path = self::pathToFile($fid, $img['doc']);
            $info = pathinfo($path.'/'.$img['original']);
            $dms = explode(',', str_replace(' ', '', $config['dimension']));
            unlink($path.'/'.$img['original']);
            foreach($dms as $d)
                unlink($path.'/'.$info['filename'].'('.$d.').'.$info['extension']);
        }
        (new SQLConstructor())->delete($fid, ['@in'=>['id', $ids]])->execute();
    }

    /**
     * Удаление значения
     *
     * @param int $docID
     * @return bool|resource|void
     */
//    public function delete($docID) {
//        $ids = [];
//        $imgs = (new SQLConstructor())->find($this->fid, ['doc'=>(int)$docID])->fields('id')->maps();
//        foreach($imgs as $i) $ids[] = $i['id'];
//        self::deleteValues($this->fid, $ids);
//        rmdir(self::pathToFile($this->fid, $docID));
//    }

    /**
     * Преобразование изображений и сохранение
     *
     * @param $name
     * @param $newName
     * @param $dimensions
     * @return bool
     */
    private static function _action($name, $newName, $dimensions) {
        if(rename($name, $newName)) {
            chmod($newName, 0755);
            if($dimensions != '') {
                $dms = explode(',', str_replace(' ', '', $dimensions));
                foreach($dms as $d) {
                    $f = pathinfo($newName);
                    list($w, $h) = explode('x', $d);
                    (new Img($newName))->resize($w, $h, true)->save($f['dirname'].'/'.$f['filename'].'('.$d.').'.$f['extension']);
                }
            }
            return true;
        } else { return false; }
    }

    /**
     * Значения прикреплённого поля
     *
     * @param $fid
     * @param $data
     * @return array
     */
    public static function resource($fid, $data) {
        $res = [];
        if(!$data) return [];

        $pth = self::pathToFile($fid, $data[0]['doc']);
        $cfg = unserialize((new SQLConstructor())->find('fields', ['fid'=>$fid])->fields('config')->map()['config']);

        $dimensions = ($cfg['dimension'] != '')?explode(',', str_replace(' ', '', $cfg['dimension'])):'';
        foreach($data as $d) {
            $f = pathinfo($d['original']); // File info
            $d['original'] = $pth.'/'.$d['original'];
            if($dimensions != '') {
                $d['thumbs'] = [];
                foreach($dimensions as $dm) $d['thumbs'][$dm] = $pth.'/'.$f['filename'].'('.$dm.').'.$f['extension'];
            }
            $res[] = $d;
        }
        return $res;
    }

    /**
     * Информация о поле
     *
     * @return array|string
     */
    public function info() {
        return [
            'title'=>'Изображение',
            'description'=>''
        ];
    }
}
