<?php
/**
 * Description of Module_Seo
 *
 * @author goncharovsv
 */
class Module_Seo extends Module {
    public function boot() {
        incFile($this->path().'/SEO.php');
    }

    public function init() {
        Observer::addListener('Document', 'view', function(&$doc) {
            $page = Page::getInstance();
            $page->setDescription($doc->fields['adt']);
            if(isset($doc->fields['field_keywords']) && $doc->fields['field_keywords'] != '')
                $page->setKeyWords($doc->fields['field_keywords']);
            else $page->setKeyWords(SEO::getTopWords($doc->fields['body'], 10, 6));
        });
    }

    public function cron() {
        $docs = (new SQLConstructor())->find('docs', ['stat'=>1])->fields('id', 'alias', 'created')->maps();
        $sm = new Smarty();
        $sm->setTemplateDir($this->path().'/');
        $sm->assign('docs', $docs);
        $sm->assign('host', $_SERVER['HTTP_HOST']);
        fileWrite('sitemap.xml', $sm->fetch('sitemap.tpl'));
    }

    public function perms() { return []; }
}