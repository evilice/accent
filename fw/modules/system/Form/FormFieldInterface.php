<?php
/**
 *
 * @author goncharovsv
 */
interface FormFieldInterface {
    // Устанавливает атрибут id
    public function id();
    
    // Устанавливает атрибут name
    public function name();
    
    // Устанавливает атрибут value
    public function value();
    
    // Устанавливает title
    public function title();
    
    // Устанавливает атрибуты
    public function attrs($attrs = false);
    
    // Устанавливает описание поля (подсказка)
    public function description($description = false);
    
    // Возвращает значение установленного параметра
    public function get($key);
    
    // Возвращает html-код элемента
    public function view();
    
    // Возвращает тип элемента
    public function type();
    
    // Возвращает форму настройки поля
    public function config();
    
    // Удаление данных
//    public function delete($docID);
}