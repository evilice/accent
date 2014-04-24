<?php
/**
 * Description of Document
 *
 * @author goncharovsv
 */
class Document {
    private $id; // Идентификатор документа
    private $uid; // Идентификатор автора
    private $top; // Всегда первый
    private $type; // Тип документа
    private $path; // Алиас
    private $stat = false; // Статус публикации
    private $created; // Дата создания
    private $createMenu = false; // Создать пункт меню
    private $menuParent; // Родительский элемент меню
    private static $defFields = ['title', 'adt', 'body'];
    private $fieldsByModule = [];

    public function getId() { return $this->id; }
    public function getUid() { return $this->uid; }
    public function getType() { return $this->type; }
    public function getStat() { return $this->stat; }
    public function getAlias() { return $this->path; }
    public function getCreated() { return $this->created; }

    private $fields = []; // Поля документа

    /**
     * Построение объекта из данных переданных из формы
     * 
     * @param Array $data
     */
    public function build($data) {
        $fields = ['id', 'top', 'type', 'stat', 'createMenu', 'menuParent', 'path', 'uid', 'created'];
        foreach($fields as $f) {
            if(isset($data[$f])) {
                $this->$f = $data[$f];
                unset($data[$f]);
            }
        }
        foreach($data as $k=>$v) $this->fields[$k] = $v;

        $list = $this->_attachedFields();
        foreach($list as $k=>$v) {
            $m = $v['module'];
            if(!isset($this->fieldsByModule[$m])) $this->fieldsByModule[$m] = [];
            $this->fieldsByModule[$m][$k] = [
                'config'=>$v['config'],
                'value'=>$this->fields[$k]
            ];
        }
    }

    /**
     * Документ
     *
     * @param $id
     * @return Array|mixed
     */
    public function get($id) {
        $doc = null;
        $ch = Cache::getdb('mdoc_'.((int)$id), 'doc_map');
        if($ch && Cache::config('cfg.ccache.page')) {
            $doc = unserialize($ch[0]['data']);
            $this->build($doc);
        } else {
            $d = new SQLConstructor();
            $d->find('docs', ['docs.id'=>(int)$id]);
            $doc = $d->map();
//            if(!$doc) var_dump($d->toString());

            $doc['title'] = htmlspecialchars_decode($doc['title']);
            $doc['adt'] = htmlspecialchars_decode($doc['adt']);
            $doc['body'] = htmlspecialchars_decode($doc['body']);
            
            /**
             * Обработка прикреплённых полей
             */
            $fields = $this->_attachedFields($doc['type']);

            foreach ($fields as $fid=>$dt) {
                $c = new SQLConstructor();
                $c->find($fid, ['doc'=>(int)$id]);
                $mps = $c->maps();
                switch($dt['module']) {
                    case 'FieldImage': { $doc[$fid] = FieldImage::resource($dt['fid'], $mps); break; }
                    case 'FieldFile': { $doc[$fid] = FieldFile::resource($dt['fid'], $mps); break; }
                    case 'FieldSelect': { $doc[$fid] = FieldSelect::resource($dt['fid'], $mps); break; }
                    default : {
                        $cfg = unserialize($dt['config']);
                        if(isset($cfg['maxLenght']) && isset($cfg['minLenght'])){
                            if($cfg['minLenght'] == 0 && $cfg['maxLenght'] == 0) {
                                $doc[$fid] = isset($mps[0])?$mps[0]['text']:'';
                            } else {
                                $doc[$fid] = $mps;                        
                            }
                        }
                    }
                }
            }
            if($doc) { 
                $this->build($doc);
                if(Cache::config('cfg.ccache.page'))
                    Cache::setdb('mdoc_'.((int)$id), serialize($doc), 'doc_map');
            }
        }
//        Observer::callEvent($doc);
        return $doc;
    }
    
    /**
     * Создание документа
     */
    public function add() {
        $user = User::getInstance();

        $c = new SQLConstructor();
        $c->insert('docs', [[
            $user->id(),
            $this->type,
            htmlspecialchars($this->fields['title']),
            htmlspecialchars($this->fields['adt']),
            htmlspecialchars($this->fields['body']),
            htmlspecialchars($this->path),
            time(),
            ($this->stat)?1:0,
            ($this->top)?1:0
        ]]);

        $c->fields('uid', 'type', 'title', 'adt', 'body', 'alias', 'created', 'stat', 'top');

        if($c->execute()) {
            $db = new DB();
            $this->id = $db->lastIndex();

            //--- Создание пункта меню
            if($this->createMenu) {
                $d = new Dictionary();
                $d->setTitle($this->fields['title']);
                $d->setParent($this->menuParent);
                $d->setCode($this->menuParent.'.'.tl(str_replace(' ', '_', $this->fields['title'])));
                if($this->path != '') $d->setVal($this->path);
                $d->add();
            }
            
            //--- Сохранение прикреплённых полей ---
            Observer::callEvent($this);
        }
    }

    /**
     * Удаление документа
     *
     * @param $id
     * @return bool
     */
    public function del() {
        if((new SQLConstructor())->delete('docs', ['id'=>(int) $this->id])->execute()) Observer::callEvent($this);
        return true;
    }

    /**
     * Сохранение документа
     *
     * @param $document
     * @return bool
     */
    public function save() {
        $c = new SQLConstructor();
        $c->update('docs', [
            'title'=>htmlspecialchars($this->fields['title']),
            'adt'=>htmlspecialchars($this->fields['adt']),
            'body'=>htmlspecialchars($this->fields['body']),
            'alias'=>htmlspecialchars($this->path),
            'stat'=>($this->stat)?1:0,
            'top'=>($this->top)?1:0
        ]);
        $c->where(['id'=>(int)$this->id]);

        //--- Сохранение прикреплённых полей ---
        if($c->execute()) { Observer::callEvent($this); }

        return true;
    }
    
    /**
     * Список документов указанного типа
     * 
     * @param String $type
     */
    public function listByType() {}
    
    /**
     * Возвращает тип документа
     * 
     * @param int $id
     * @return String
     */
    public function getDocumentType($id) {
        return (new SQLConstructor())->find('docs d', ['d.id'=>(int)$id])->join('doc_types', 't', ['@eqf'=>['d.type', 't.name']], 'left')->fields('t.*')->map();
    }
    
    /**
     * Список дополнительных полей
     * @return type
     */
    private function _attachedFields($type = false) {
        $type = ($type)?$type:$this->type;
        $fl = new SQLConstructor();
        $fl->find('doc_type_fields tf, fields f', [
            '@and'=>[
                ['tf.type'=>$type],
                ['@eqf'=>['tf.fid', 'f.fid']],
                ['@notin'=>['tf.fid', self::$defFields]]
            ]]);
        $fl->fields('tf.fid, f.module, f.config');
        
        return $fl->maps('fid');
    }

    /**
     * Подготовка данных для вывода в шаблоне
     *
     * @return stdClass
     */
    public function view() {
        $d = new stdClass();
        $d->id = $this->id;
        $d->uid = $this->uid;
        $d->type = $this->type;
        $d->created = $this->created;
        $d->path = $this->path;
        $d->fields = $this->fields;

        Observer::callEvent($d);
        return $d;
    }


    /**
     * Список полей документа
     *
     * @return array
     */
    public function fields() { return $this->fields; }

    public function fieldsByModule() {
        return $this->fieldsByModule;
    }
    
    /**
     * Список полей по умолчанию
     * @return type
     */
    public static function defFields() {
        return self::$defFields;
    }
}