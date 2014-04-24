<?php
/**
 * Description of FieldCheckbox
 *
 * @author SP
 */
class FieldCheckbox extends FormField {
    private $checked = false;
    
    public function checked($checked) {
        $this->checked = $checked;
    }
    public function isChecked() { return $this->checked; }
    
    public function config() {
        return [];
    }
    
    public function info() {
        return [
            'title'=>'Флаг',
            'description'=>''
        ];
    }
}