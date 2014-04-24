<?php
/**
 * Created by JetBrains PhpStorm.
 * User: goncharovsv
 * Date: 20.03.13
 * Time: 16:49
 * To change this template use File | Settings | File Templates.
 */
class Module_Downloader extends Module {
    public function controllers() {
        return [
            'download'=>['title'=>'', 'callback'=>'download']
        ];
    }

    public function download($fid, $id) {
        $file = (new SQLConstructor())->find($fid, ['id'=>(int)$id])->map();
        var_dump($file);
    }
}
