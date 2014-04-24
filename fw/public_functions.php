<?php
/**
 * Подключение файла
 * @param type $file
 */
function incFile($file) {
    if(file_exists($file)) include_once $file;
}

/**
 * Подключение списка файлов
 */
function incFiles() {
    $files = func_get_args();
    foreach ($files as $fl) incFile($fl);
}

/**
 * Возвращает список директорий и файлов по указанному пути
 * 
 * @param String $dir
 * @return Array OR FALSE
 */
function read_dir($dir) {
    if(is_dir($dir)) {
        $files = scandir($dir);
        return array_slice($files, 2, count($files));
    } return false;
}

/**
 * Подключение фсех файлов в указанной директории
 * 
 * @param String $dir
 */
function incDir($dir) {
    $files = read_dir($dir);
    $count = count($files);
    for($i=0; $i<$count; $i++) {
        $path = $dir.$files[$i];
        if(!is_dir($path)) include_once $path;
    }
}

/**
 * Подключение всех фалов в указанных директориях
 */
function incDirs() {
    $args = func_get_args();
    while($arg = array_shift($args))
        incDir ($arg);
}

/**
 * Чтение содержимого файла
 */
function fileRead($file) {
    return (file_exists($file))?file_get_contents($file):false;
}

/**
 * Запись содержимого в файл
 * 
 * @param String $content
 */
function fileWrite($path, $content) {
    file_put_contents($path, $content);
}

/**
 * Удаление файла
 * 
 * @param String $file
 * @return bool
 */
function fileDel($file) { return unlink($file); }

/**
 * Удаление файлов из директории
 * 
 * @param String $dir
 */
function fileDelFromDir($dir) {
    $list = scandir($dir);
    for($i=2; $i<count($list); $i++) {
        fileDel($dir.$list[$i]);
    }
}

/**
 * Разбивает URL на составные
 * 
 * @return array
 */
function args($pos = false) {
    $q = url();
    $ls = ($q != '')?explode('/', $q):[];
    return ($pos !== false)?(isset($ls[$pos])?$ls[$pos]:''):$ls;
}

function url() {
    $g=isset($_GET['q'])?$_GET['q']:false;
    return (!$g?'front':trim($g, '/'));
}

/**
 * Генерация случайной строки
 * @return String
 */
function randstr() { return md5(uniqid(rand(1, time()), 1)); }

/**
 * Путь до модуля относительно корня сайта
 * 
 * @param String $moduleName
 * @return String
 */
function pathToModule($moduleName) {
    $pth = 'fw/modules/';
    return (is_dir($pth.'system/'.$moduleName))?
        $pth.'system/'.$moduleName:$pth.'all/'.$moduleName;
}

/**
 * Путь до шаблона
 * 
 * @param String $templateName
 * @return String
 */
function pathToTemplate($templateName) {
    $p = 'templates/'.$templateName;
    return (is_dir($p))?$p:false;
}

/**
 * Переводчик (транслейтор)
 * 
 * @param String $key
 * @return String
 */
function t($key) {
    return System::getInstance()->t($key);
}

/**
 * Транслитерация
 *
 * @param $str
 * @return mixed
 */
function tl($str) {
    // Русский алфавит
    $rus_alphabet = array(
        'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й',
        'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф',
        'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я',
        'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й',
        'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф',
        'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я'
    );

    // Английская транслитерация
    $rus_alphabet_translit = array(
        'A', 'B', 'V', 'G', 'D', 'E', 'IO', 'ZH', 'Z', 'I', 'I',
        'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F',
        'H', 'C', 'CH', 'SH', 'SH', '`', 'Y', '`', 'E', 'IU', 'IA',
        'a', 'b', 'v', 'g', 'd', 'e', 'io', 'zh', 'z', 'i', 'i',
        'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f',
        'h', 'c', 'ch', 'sh', 'sh', '`', 'y', '`', 'e', 'iu', 'ia'
    );

    return str_replace($rus_alphabet, $rus_alphabet_translit, $str);
}

/**
 * Отчистка строки от лишних пробелов
 * 
 * @param String $str
 * @return String
 */
function str_clear($str)   { 
    $str = preg_replace('/  +/i', '', str_replace('\r\n', '', $str));
    return preg_replace("/(\r\n)+/i", "", $str);
}

/**
 * Перенаправление на страницу
 * @param String $url
 */
function g2p($url) { header('Location: '.$url); }

/**
 * Подключение JS файлов к странице
 */
function js() {
    $files = func_get_args();
    $page = Page::getInstance();
    foreach($files as $fl) $page->js($fl);
}
/**
 * Подключение CSS файлов к странице
 */
function css() {
    $files = func_get_args();
    $page = Page::getInstance();
    foreach($files as $fl) $page->css($fl);
}