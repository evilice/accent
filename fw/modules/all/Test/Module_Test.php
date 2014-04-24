<?php
/**
 * Created by JetBrains PhpStorm.
 * User: S.Goncharov
 * Date: 03.03.13
 * Time: 18:07
 * To change this template use File | Settings | File Templates.
 */
class Module_Test extends Module {
    public function controllers() {
        return [
            'test'=>['title'=>'', 'callback'=>'page']
        ];
    }

    public function page() {
        return Cache::getdb(md5('document/20'), 'seo');
    }
}
