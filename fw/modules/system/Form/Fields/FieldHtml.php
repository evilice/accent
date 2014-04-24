<?php
/**
 * Description of FieldHtml
 *
 * @author goncharovsv
 */
class FieldHtml {
    private $html = '';

    public static function listners() {}

    public function __construct($html = false) {
        if($html) $this->html = $html;
    }
    
    public function html($html = false) {
        if($html) $this->html = $html;
        else return $this->html;
    }
    
    public function type() { return __CLASS__; }
    
    public function view() { return $this->html; }
}