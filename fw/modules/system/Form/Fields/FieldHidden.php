<?php
/**
 * Description of FieldHidden
 *
 * @author SP
 */
class FieldHidden extends FormField {
    public function __construct($attributes = false) {
        if($attributes)
            foreach ($attributes as $k=>$v)
                $this->attrs[$k] = $v;
    }
}