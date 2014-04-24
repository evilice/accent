<?php
/**
 * Description of FieldPassword
 *
 * @author goncharovsv
 */
class FieldPassword extends FormField {
    public function config() {
        $title = new FieldText('Заголовок', ['name'=>'title']);
        $mashineName = new FieldText('Машинное имя', ['name'=>'mashineName']);
        $maxLength = new FieldText('Максимальная длина строки', ['name'=>'maxLenght']);
        $minLength = new FieldText('Минимальная длина строки', ['name'=>'minLenght']);
        
        return [$title, $mashineName, $minLength, $maxLength];
    }
    
    public function info() {
        return [
            'title'=>'Пароль',
            'description'=>''
        ];
    }
}
