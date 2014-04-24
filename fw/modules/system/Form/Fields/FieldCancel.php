<?php
/**
 * Description of FieldCancel
 *
 * @author goncharovsv
 */
class FieldCancel extends FormField {
    public function __construct($title = false, $go2url = '', $attributes = false) {
        $attributes['onclick'] = "g2p('".$go2url."'); return false;";
        parent::__construct($title, $attributes = $attributes);
    }
}
