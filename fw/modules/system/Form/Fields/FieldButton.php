<?php
/**
 * Description of FieldButton
 *
 * @author SP
 */
class FieldButton extends FormField {
    private $events = []; //--- Обработчики событий
    
    /**
     * Устанавливает или возвращает список событий
     * 
     * @param array $events
     * @return array
     */
    public function events($events = false) {
        if($events) $this->events = $events;
        else return $this->events;
    }
}