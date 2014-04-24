<?php
/**
 * Description of Module_Form
 *
 * @author goncharovsv
 */
class Module_Form extends Module {
    public function boot() {
        $p = $this->path();
        incFiles($p.'/Form.php',
                 $p.'/FormFieldInterface.php',
                 $p.'/FormField.php');
        incDir($p.'/Fields/');
        $list = read_dir($p.'/Fields/');
        foreach($list as $l) {
            $md = (substr($l, 0, strrpos($l, '.')));
            $md::listners();
        }
    }

    public function controllers() {
        return [
            'admin/field_value/delete'=>['callback'=>'deleteValue', 'perms'=>['document edit']]
        ];
    }

    public function deleteValue() {
        $f = (new SQLConstructor())->find('fields', ['fid'=>$_POST['fid']])->map();
        $f['module']::deleteValues($_POST['fid'], $_POST['id']);
        return '';
    }
    
    public function init() {
        $sys = System::getInstance();
        $p = $_POST;
        if(isset($p['_form_uid'])) {
            if(isset($_SESSION['forms'][$p['_form_uid']])) {
                $cb = $_SESSION['forms'][$p['_form_uid']];
                unset($_POST['_form_uid']);
                $md = new $cb['class']();
                $md->$cb['callback']();
                Message::save();
                header('Location: '.$_SERVER['REQUEST_URI']);
                exit();
            } else Message::add ('При отправке данных произошла ошибка.', Message::MESSAGE_ERROR);
        }
        if(!$sys->isAJAX()) $_SESSION['forms'] = [];
    }
}