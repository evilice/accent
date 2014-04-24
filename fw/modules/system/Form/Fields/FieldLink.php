<?php
/**
 * Ссылка
 *
 * @author goncharovsv
 */
class FieldLink extends FormField {
    public function __construct($title=false, $href=false, $attrs = false) {
        $this->title($title);
        $this->attrs(['href'=>$href]);
        $this->attrs($attrs);
    }
}