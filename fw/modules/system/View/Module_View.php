<?php
/**
 * Модуль управления представлениями
 *
 * @author goncharovsv
 */
class Module_View extends Module {
    private $p = 'admin/structure/views';

    /**
     * Загрузка модуля
     */
    public function boot() {
        $p = $this->path();
        incFile($p.'/ViewInterface.php');
        incDir($p.'/views/');
    }

    /**
     * Контроллеры
     *
     * @return array
     */
    public function controllers() {
        $p = $this->p;
        $url = [
            'request/'.$p=>['title'=>'', 'callback'=>'ajax', 'perms'=>['views']],
            $p=>['title'=>'Представления', 'callback'=>'views', 'perms'=>['views']],
            $p.'/add'=>['title'=>'Создать представление', 'callback'=>'formAdd', 'perms'=>['views']],
            $p.'/del'=>['title'=>'Удалить представление', 'callback'=>'formDel', 'perms'=>['views']],
            $p.'/edit'=>['title'=>'Редактировать представление', 'callback'=>'formEdit', 'perms'=>['views']]
        ];
        $c = new SQLConstructor();
        $list = $c->find('views', ['@like'=>['`type`', '%pg%']])->maps();
        foreach ($list as $u) {
            $data = unserialize($u['data']);
            $url[$data['alias']] = ['title'=>$u['title'], 'callback'=>'page', 'perms'=>[], 'args'=>[$u['name'], $data]];
        }
        return $url;
    }

    /**
     * Список представлений
     *
     * @return string
     */
    public function views() {
        $data = [];
        $c = new SQLConstructor();
        $views = $c->find('views')->maps();

        foreach($views as $v) {
            $data[] = [
                $v['title'],
                $v['name'],
                $v['description'],
                '<a href="/'.$this->p.'/del/'.$v['name'].'">Удалить</a>',
                '<a href="/'.$this->p.'/edit/'.$v['name'].'">Редактировать</a>'
            ];
        }
        $vt = new ViewTable();
        $vt->data($data);

        $bt = new FieldButton('Добавить представление');
        $bt->events(['onclick'=>"g2p('/".$this->p."/add');"]);

        return $bt->view().$vt->view();
    }

    /**
     * Форма создания представления
     *
     * @return type
     */
    public function formAdd() {
        $page = Page::getInstance();
        $page->js($this->path().'/js/view.js');
        $page->css('/'.$this->path().'/css/view.css');
        $page->css('/'.$this->path().'/css/ViewFields.css');
        $page->jstmpls('menu_selected_tpl', fileRead($this->path().'/tmpls/menu_select.tpl'));

        $f = new Form('Добавить новое представление', ['action'=>'/'.$this->p]);
        $f->callback('addView');

        $title = new FieldText('Заголовок', ['name'=>'title']);
        $name = new FieldText('Машинное имя (a-z)', ['name'=>'mash']);
        $desc = new FieldTextarea('Описание', ['name'=>'description']);
        $html = new FieldHtml();
        $add = new FieldSubmit('Создать');
        $cnl = new FieldButton('Отмена');
        $cnl->events(['onclick'=>"g2p('/".$this->p."');return false;"]);
        $box = new FieldGroup([$add, $cnl]);

        $blCreate = new FieldCheckbox('Создать блок', ['name'=>'blCreate', 'id'=>'blCreate']);
        $blCache = new FieldCheckbox('Разрешить кеширование', ['name'=>'blCache', 'id'=>'blCache', 'disabled'=>'disabled']);
        $blUrls = new FieldTextarea('Адреса по которым будет доступен блок', ['name'=>'vs', 'id'=>'blUrls', 'disabled'=>'disabled']);
        $blNUrls = new FieldTextarea('Адреса по которым будет недоступен блок', ['name'=>'unv', 'id'=>'blNUrls', 'disabled'=>'disabled']);

        $pgCreate = new FieldCheckbox('Создать страницу', ['name'=>'pgCreate', 'id'=>'pgCreate']);
        $pgMenu = new FieldCheckbox('Создать пункт меню', ['name'=>'pgMenu', 'id'=>'pgMenu', 'disabled'=>'disabled']);
        $pgMenuSelected = new FieldSelect('Меню', ['name'=>'pgMenuSelected', 'id'=>'pgMenuSelected', 'disabled'=>'disabled']);
        $pgCache = new FieldCheckbox('Разрешить кеширование', ['name'=>'pgCache', 'id'=>'pgCache', 'disabled'=>'disabled']);
        $pgAlias = new FieldText('Алиас', ['name'=>'pgAlias', 'id'=>'pgAlias', 'disabled'=>'disabled']);

        $v = new ViewTab();
        $v->type(ViewTab::LINE_HOROZONTAL);
        $v->tab('Блок', $blCreate->view().$blCache->view().$blUrls->view().$blNUrls->view());
        $v->tab('Страница', $pgCreate->view().$pgCache->view().$pgMenu->view().$pgMenuSelected->view().$pgAlias->view());
        $sm = new Smarty();
        $sm->setTemplateDir($this->path() . '/tmpls/');
        $behContent = new FieldSelect('', ['name'=>'content', 'id'=>'flContent']);
        $behContent->option('Документ', 'document');
        $behContent->option('Словарь', 'dictionary');
        $behContent->option('Таблицу из БД', 'db');
        $sm->assign('select', $behContent->view());
        $cnt = new FieldText('Количество выводимых объектов', ['name'=>'count']);
        $rws = new FieldText('Количество выводимых объектов на странице', ['name'=>'rows']);
        $v->tab('Поведение', $sm->fetch('behavior.tpl').$cnt->view().$rws->view());
        $html->html($v->view());

        $f->fields($title, $name, $desc, $html, $box);

        return $f->view();
    }

    /**
     * Форма удаления представления
     *
     * @return string
     */
    public function formDel($view) {
        $f = new Form('Удалить представление', ['action'=>'/'.$this->p]);
        $f->callback('delview');

        $cn = new FieldButton('Отмена');
        $cn->events(['onclick'=>"g2p('/".$this->p."');return false;"]);
        $f->fields(new FieldSubmit('Удалить'), new FieldHidden(['name'=>'view', 'value'=>$view]), $cn);

        return $f->view();
    }

    /**
     * Форма редактирования представления
     * @param type $view
     * @return type
     */
    public function formEdit($view) {
        $page = Page::getInstance();
        $page->js($this->path().'/js/view.js');
        $page->css('/'.$this->path().'/css/view.css');
        $page->css('/'.$this->path().'/css/ViewFields.css');
        $page->jstmpls('menu_selected_tpl', fileRead($this->path().'/tmpls/menu_select.tpl'));

        $f = new Form('Редактировать представление', ['action'=>'/'.$this->p]);
        $f->callback('editView');

        // информация о редактируемом представлении
        $c = new SQLConstructor();
        $c->find('views', ['name' => $view]);
        $data = $c->map();
        $extra_data = unserialize($data['data']);

        // элементы формы
        $title = new FieldText('Заголовок', ['name'=>'title', 'value'=>$data['title']]);
        $name = new FieldText('Машинное имя (a-z)', ['disabled'=>'disabled', 'value'=>$data['name']]);
        $desc = new FieldTextarea('Описание', ['name'=>'description', 'value'=>$data['description']]);
        $html = new FieldHtml();
        $add = new FieldSubmit('Сохранить');
        $cnl = new FieldButton('Отмена');
        $cnl->events(['onclick'=>"g2p('/".$this->p."');return false;"]);
        $box = new FieldGroup([$add, $cnl]);

        // информация о созданных блоках
        $b = new SQLConstructor();
        $b->find('blocks', ['conf' => $data['name']]);
        $b->fields('id');
        $block = $b->map();
        $block_data = [
            'vs' => '',
            'unv' => ''
        ];
        if($block){
            $block = new Block($block['id']);
            $block_data['vs'] = implode("\n", $block->getUrls()['vs']);
            $block_data['unv'] = implode("\n", $block->getUrls()['unv']);
        }
        $blCreateParams = ['name'=>'blCreate', 'id'=>'blCreate'];
        $blCacheParams = ['name'=>'blCache', 'id'=>'blCache', 'disabled'=>'disabled'];
        $blUrlsParams = ['name'=>'vs', 'id'=>'blUrls', 'disabled'=>'disabled'];
        $unvUserParams = ['name'=>'unv', 'id'=>'blNUrls', 'disabled'=>'disabled'];
        $blCreateHidden = new FieldHidden();
        if($block != null){
            $blCreateParams['checked'] = 'checked';
            $blCreateParams['disabled'] = 'disabled'; // если блок уже создан
            $blCreateHidden->attrs(['name'=>'blCreate', 'checked'=>'checked']);
            unset($blCacheParams['disabled']);
            unset($blUrlsParams['disabled']);
            unset($unvUserParams['disabled']);
        }
        if(isset($extra_data['bl_cache']) && $extra_data['bl_cache']){
            $blCacheParams['checked'] = 'checked';
        }
        $blCreate = new FieldCheckbox('Создать блок', $blCreateParams);
        $blCache = new FieldCheckbox('Разрешить кеширование', $blCacheParams);

        // Адреса по которым будет доступен блок
        if(!empty($block_data['vs'])){
            $blUrlsParams['value'] = $block_data['vs'];
        }
        $blUrls = new FieldTextarea('Адреса по которым будет доступен блок', $blUrlsParams);

        // Адреса по которым будет недоступен блок
        if(!empty($block_data['unv'])){
            $unvUserParams['value'] = $block_data['unv'];
        }
        $blNUrls = new FieldTextarea('Адреса по которым будет недоступен блок', $unvUserParams);

        // информация о созданной странице и меню
        $pgCreateParams = ['name'=>'pgCreate', 'id'=>'pgCreate'];
        $pgMenuSelectedParams = ['name'=>'pgMenuSelected', 'id'=>'pgMenuSelected', 'disabled'=>'disabled'];
        $pgMenuParams = ['name'=>'pgMenu', 'id'=>'pgMenu', 'disabled'=>'disabled'];
        $pgAliasParams = ['name'=>'pgAlias', 'id'=>'pgAlias', 'disabled'=>'disabled'];
        $pgCacheParams = ['name'=>'pgCache', 'id'=>'pgCache', 'disabled'=>'disabled'];

        $pgCreateHidden = new FieldHidden();

        if($data['type'] == 'pg' || $data['type'] == 'blpg'){
            $pgCreateParams['checked'] = 'checked';
            $pgCreateParams['disabled'] = 'disabled'; // если страница уже создана
            $pgCreateHidden->attrs(['name'=>'pgCreate', 'checked'=>'checked']);
            unset($pgMenuParams['disabled']);
            unset($pgAliasParams['disabled']);
            unset($pgCacheParams['disabled']);
        }
        if(isset($extra_data['pg_cache']) && $extra_data['pg_cache']){
            $pgCacheParams['checked'] = 'checked';
        }
        $pgCreate = new FieldCheckbox('Создать страницу', $pgCreateParams);

        // выбранное меню
        $pgMenuSelectedOptions = [];

        $pgMenuHidden = new FieldHidden();
        if(!empty($extra_data['alias'])){
            $m = new SQLConstructor();
            $m->find('dictionary');
            $m->fields('parent');
            $m->where(['@like' => ['code', '%' . $extra_data['alias']]]);
            if($selectedMenu = $m->map()){
                $pgMenuParams['checked'] = 'checked';
                $pgMenuParams['disabled'] = 'disabled';
                unset($pgMenuSelectedParams['disabled']);
                $pgMenuHidden->attrs(['name'=>'pgMenu', 'checked'=>'checked']);
                $m = new SQLConstructor();
                $m->find('dictionary');
                $m->fields('code, title');
                $m->where(['parent' => 'menu']);
                $pgMenuSelectedOptions = $m->maps();
            }
        }
        $pgMenu = new FieldCheckbox('Создать пункт меню', $pgMenuParams);
        $pgMenuSelected = new FieldSelect('Меню', $pgMenuSelectedParams);
        foreach($pgMenuSelectedOptions as $option){
            $pgMenuSelected->option($option['title'], $option['code'], $selectedMenu['parent'] == $option['code']);
        }

        // Кэширование страницы
        $pgCache = new FieldCheckbox('Разрешить кеширование', $pgCacheParams);
        // Алиас страницы

        if(isset($extra_data['alias'])){
            $pgAliasParams['value'] = $extra_data['alias'];
        }
        $pgAlias = new FieldText('Алиас', $pgAliasParams);

        $v = new ViewTab();
        $v->type(ViewTab::LINE_HOROZONTAL);
        $v->tab('Блок', $blCreate->view().$blCache->view().$blUrls->view().$blNUrls->view());
        $v->tab('Страница', $pgCreate->view().$pgCache->view().$pgMenu->view().$pgMenuSelected->view().$pgAlias->view());

        // Поведение
        $sm = new Smarty();
        $sm->setTemplateDir($this->path() . '/tmpls/');

        // Выбор вариант отображения
        $behContentHidden = new FieldHidden();
        $behContent = new FieldSelect('', ['name'=>'content', 'id'=>'flContent', 'disabled'=>'disabled']);
        $behContent->option('Документ', 'document', $extra_data['document'] == 'document');
        $behContent->option('Словарь', 'dictionary', $extra_data['document'] == 'dictionary');
        $behContent->option('Таблицу из БД', 'db', $extra_data['document'] == 'db');
        $sm->assign('select', $behContent->view());
        $typesSelectHidden = new FieldHidden();
        $typesSelect = new FieldSelect('', ['name'=>'docType', 'id'=>'docType', 'disabled'=>'disabled']);

        // Для варианта отображения "Документ"
        if($extra_data['document'] == 'document'){
            $behContentHidden->attrs(['name'=>'content', 'value'=>$extra_data['document']]);
            $typesSelectHidden->attrs(['name'=>'docType', 'value'=>$extra_data['type']]);
            // Формирования списка типов документов
            $t = new DocumentType($extra_data['type']);
            $typesSelect->option('', '');
            foreach($t->types() as $type){
                $typesSelect->option($type['title'], $type['name'], $type['name'] == $extra_data['type']);
            }
            // Если выбран конкретный тип документа
            if(!empty($extra_data['type'])){
                $behaviorFields = new Smarty();
                $behaviorFields->setTemplateDir($this->path() . '/tmpls/');
                $behaviorFields->assign('fields', $t->fields());
                $behaviorFields->assign('config', $extra_data);
                $behaviorFields->assign('filters', [
                    '@gt'=>'Больше чем',
                    '@lt'=>'Меньше чем',
                    '@like'=>'Включает в себя',
                    '@notlike'=>'Не включает в себя'
                ]);
                $sm->assign('data', $behaviorFields->fetch('fields.tpl'));
            }
        }

        $sm->assign('types', $typesSelect->view());
        $cnt = new FieldText('Количество выводимых объектов', ['name'=>'count', 'value'=>$extra_data['count']]);
        $rws = new FieldText('Количество выводимых объектов на странице', ['name'=>'rows', 'value'=>$extra_data['rows']]);
        $v->tab('Поведение', $sm->fetch('behavior.tpl').$cnt->view().$rws->view());
        $html->html($v->view());

        $f->fields(
            $title,
            $name,
            $desc,
            $html,
            $box,
            new FieldHidden(['name'=>'view', 'value'=>$view]),
            new FieldHidden(['name'=>'mash', 'value'=>$data['name']]),
            $blCreateHidden,
            $pgCreateHidden,
            $pgMenuHidden,
            $behContentHidden,
            $typesSelectHidden
        );

        return $f->view();
    }

    public function delView() {
        $n = $_POST['view'];
        $v = new SQLConstructor();
        $v->delete('views', ['name'=>$n])->execute();

        $b = new SQLConstructor();
        $b->delete('blocks', ['conf'=>$n])->execute();
    }

    /**
     * Создание представления
     */
    public function addView() {

        $p = $_POST;
        $data = [];

        switch ($p['content']) {
            case 'document':
                $data = [
                    'document'=>(isset($p['content']) ? $p['content'] : ''),
                    'type'=>(isset($p['docType']) ? $p['docType'] : ''),
                    'fields'=>(isset($p['fields']) ? json_decode($p['fields'], true) : ''),
                    'sort'=>(isset($p['sort']) ? json_decode($p['sort'], true) : ''),
                    'count'=>(isset($p['count']) ? $p['count'] : ''),
                    'rows'=>(isset($p['rows']) ? $p['rows'] : ''),
                    'filter'=>(isset($p['filter']) ? json_decode($p['filter'], true) : '')
                ];

                break;
            case 'dictionary':
                $data = [
                    'document'=>$p['content'],
                    'code'=>$p['dcode']
                ];
                break;

            default:
                break;
        }

        //--- Определяем тип записи в БД (блок, стриница, страница+блок)
        $tp = (isset($p['blCreate']))?'bl':'';
        if(isset($p['pgCreate'])) {
            $tp.= 'pg';
            $data['alias'] = isset($p['pgAlias']) ? $p['pgAlias'] : '';

            if(isset($p['pgMenuSelected'])){
                if(empty($p['pgAlias'])){
                    Message::add('Поле "Алиас" обязательно для заполнения', Message::MESSAGE_ERROR);
                    return;
                }

                // Удаляем существующие пункты меню для представления
                $m = new SQLConstructor();
                $m->delete('dictionary', ['@and' => [
                    ['@like' => ['code', '%' . $p['pgAlias']]],
                    ['@like' => ['parent', 'menu%']]
                ]]);
                $m->execute();

                $c = new SQLConstructor();
                $c->fields('code', 'parent', 'title', 'val', 'stat', 'weight');
                $c->insert('dictionary', [[
                    $p['pgMenuSelected'] . '.' .$p['pgAlias'],
                    $p['pgMenuSelected'],
                    $p['title'],
                    'default',
                    'default',
                    'default']], ['title', 'val', 'stat', 'weight']);

                $c->execute();
            }
        }

        $data['bl_cache'] = isset($p['blCache']);
        $data['pg_cache'] = isset($p['pgCache']);

        $c = new SQLConstructor();
        $c->insert('views',
            [[$p['mash'], $tp, $p['title'], $p['description'], serialize($data), 0]],
            ['type', 'title', 'description', 'data', 'cache']
        );
        $c->execute();
        //--- Если создаётся блок
        if(isset($p['blCreate'])) {
            $urls = [
                'vs'=>$p['vs']!=''?preg_split('/\\r\\n?|\\n/', $p['vs']):'',
                'unv'=>$p['unv']!=''?preg_split('/\\r\\n?|\\n/', $p['unv']):''
            ];
            $dc = new Dictionary();
            $dc->setCode('cfg.templates.client');
            $tmpl = $dc->get();
            if($tmpl) $tmpl = $tmpl['val'];

            if(isset($p['view'])){
                $c = new SQLConstructor();
                $c->find('blocks', ['conf' => $p['view']]);
                $c->fields('id');
                $bId = $c->map();

                if($bId){
                    $b = new Block($bId);
                    $b->setUrls([
                        'vs'=>$p['vs'],
                        'unv'=>$p['unv']]);
                    $b->save();
                }
                else{
                    $b = new Block();
                    $b->add($p['title'], 'Module_View', 'block', $urls, false, $tmpl, $p['mash']);
                }
            }
            else{
                $b = new Block();
                $b->add($p['title'], 'Module_View', 'block', $urls, false, $tmpl, $p['mash']);
            }
        }
    }

    /**
     * Редактирование представления
     * @return type
     */
    public function editView() {
        $this->addView();
    }

    /**
     *
     * @param string $view
     * @return string
     */
    public function block($view) {
        $page = Page::getInstance();
        $tpl = pathToTemplate($page->getTemplate()).'/';
        $res = '';
        $c = new SQLConstructor();
        $c->find('views', ['name'=>$view]);
        $v = $c->fields('title', 'data')->map();
        $info = unserialize($v['data']);

        switch($info['document']){
            //--- Список документов
            case 'document': {
                $docs = $this->docs($info);
                $sm = new Smarty();
                $sm->setTemplateDir($tpl);
                $sm->assign('docs', $docs);
                $res .= $sm->fetch('block_views_'.$view.'.tpl');

                break;
            }
            //--- Список терминов словаря
            case 'dictionary': {
                $dc = new Dictionary();
                $dc->setCode($info['code']);
                $sm = new Smarty();
                $sm->setTemplateDir($tpl);
                $sm->assign('items', $dc->childs());
                $res .= $sm->fetch('block_views_'.$view.'.tpl');
                break;
            }
        }

        return ['header'=>$v['title'], 'content'=>$res];
    }

    private function docs($info, $filters = [], $page=0, $callback = false) {
        /*
         * Поиск прикрепленных полей
         */
        $r = new SQLConstructor();
        $r->find('doc_type_fields tf, fields f');
        $r->fields('tf.fid, f.module');
        $r->where([
            '@and' => [
                ['tf.type'=>$info['type']],
                ['@eqf'=>['tf.fid', 'f.fid']],
                ['@notin'=>['tf.fid', Document::defFields()]]
            ]
        ]);
        $attachedFields = $r->maps('fid');

        /**
         * Поиск документов по переданным параметрм
         */
        $r = new SQLConstructor();
        $r->find('docs', ['type'=>$info['type'], 'stat'=>'1']);

        // Сортировка
        if(isset($info['sort']) && is_array($info['sort'])){
            $sorts = [];
            foreach($info['sort'] as $sort){
                if(isset($attachedFields[$sort['fid']])
                    && $attachedFields[$sort['fid']]['module'] == 'FieldText'){
                    $sort['fid'] .= '.text';
                }
                else{
                    $sort['fid'] = 'docs.'.$sort['fid'];
                }
                $sorts[] = implode(' ', $sort);
            }
            $r->sort(implode(',', $sorts));
        }

        // Фильтрация
        if(isset($info['filter']) && is_array($info['filter'])){
            $cond = [];
            foreach($info['filter'] as $filter){
                $value = $filter['value'];
                if(mb_strpos($filter['value'], '{num:') >= 0) {
                    preg_match('#\{(.*)\}#', $filter['value'], $match);
                    if($match) {
                        list($cmd, $val) = explode(':', $match[1]);
                        if(!isset($filters[$val])) $value='';
                        else if($cmd == 'num') $value = str_replace('{num:'.$val.'}', $filters[$val], $value);
                    }
                }
                if(!in_array($filter['fid'], ['stat', 'top'])) {
                    $cond[] = [$filter['condition'] => [$filter['fid'].'.val', $value]];
                } else $cond[] = [$filter['condition'] => ['docs.'.$filter['fid'], $value]];
            }
            $cond[] = ['docs.type'=>$info['type']];

            $r->where(['@and' => $cond]);
        }

        // Склеивание с внешними таблицами
        foreach($attachedFields as $field){
            $r->join($field['fid'], '', [
                '@eqf' => [$field['fid'].'.doc', 'docs.id']
            ], 'left');
        }

        $r->fields('distinct(docs.id)');

        $s = (isset($info['rows']))?(int)$info['rows']:0;
        $c = (isset($info['count']))?(int)$info['count']:0;
        if($callback) {
            $tr = $r;
            $cnt = $tr->count();
            call_user_func($callback, ($cnt>$c && $c>0)?$c:$cnt);
        }

        // Ограничение количества выдачи
        if($s > 0) {
            $ls = $page*$s;
            $le = ($ls+$s < $c || $c == 0)?$s:$s-($c-$ls);
            $r->limit($ls, $le);
        } elseif($c > 0){ $r->limit($c); }

        $doc_list = $r->maps();
        $docs = [];
        //die;
        //--- Перебор документов
        foreach ($doc_list as $id) {
            $doc = new Document();
            $doc = $doc->get($id['id']);
            $docs[] = $doc;
        }
        return $docs;
    }

    /**
     * Вывод страницы с документом/ми
     *
     * @param string $view
     * @param array $info
     * @return string
     */
    public function page($view, $info, $filters = false) {
        $np = 0;
        if($filters) {
            $cnt = count($filters);
            $np = $filters[$cnt-1]; //--- Номер страницы
            if(substr($np, 0, 3) == 'pg_') {
                $np = (int)substr($np, 3);
                array_pop($filters);
            }
        }
        $page = Page::getInstance();
        $elements = 0;
        $docs = $this->docs($info, $filters, ($np>0)?$np-1:$np, function($cnt) use (&$elements) {
            $elements = $cnt;
        });

        //--- Генерируем и возвращаем html стриницы
        $sm = new Smarty();

        $tpl_name = 'docs_list.tpl';
        $tpl_dir = $this->path() . '/tmpls/';
        $tpl_tmp = pathToTemplate($page->getTemplate()).'/'; // папка темы

        if(file_exists($tpl_tmp . 'docs_list_'.$view.'.tpl')){
            $tpl_name = 'docs_list_'.$view.'.tpl';
            $tpl_dir = $tpl_tmp;
        }
        else if(file_exists($tpl_tmp . 'docs_list.tpl')){
            $tpl_name = 'docs_list.tpl';
            $tpl_dir = $tpl_tmp;
        }

        $sm->setTemplateDir($tpl_dir);
        $sm->assign('docs', $docs);
        $sm->assign('fields', $info['fields']);
        $sm->assign('dictionary', new Dictionary());

        //--- Определение URL
        $tm = array_filter(args(), function($el) { return !($el == '');});
        if($np > 0) array_pop($tm);

        $sm->assign('pagination', (new Pagination($elements, $info['rows'], implode('/', $tm).'/pg_', ($np==0)?1:$np))->view());

        return $sm->fetch($tpl_name);
    }

    /**
     * Контроллкер AJAX-запросов
     *
     * @param string $cmd
     */
    public function ajax($cmd) {
        $p = $_POST;
        $res = [];

        switch ($cmd) {
            //--- Список типов документов
            case 'types':
                $t = new DocumentType();
                $res = $t->types();
                break;
            //--- Список полей документа
            case 'fields':
                $t = new DocumentType($p['type']);

                $sm = new Smarty();
                $sm->setTemplateDir($this->path() . '/tmpls/');
                $sm->assign('fields', $t->fields());
                $sm->assign('filters', [
                    '@gt'=>'Больше чем',
                    '@lt'=>'Меньше чем',
                    '@like'=>'Включает в себя',
                    '@notlike'=>'Не включает в себя'
                ]);
                return $sm->fetch('fields.tpl');
                break;
        }

        return json_encode($res);
    }

    /**
     * Правила доступа
     *
     * @return array
     */
    public function perms() {
        return ['views'=>'Управление представлениями'];
    }
}