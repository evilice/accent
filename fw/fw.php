<?php
ini_set('display_errors', '1');
ini_set('date.timezone', 'Asia/Irkutsk');

define('DB_CONNECT_DBNAME', 'ironnet');
define('DB_CONNECT_USER', 'root');
define('DB_CONNECT_PASS', 'SPmaloy01');
define('DB_CONNECT_HOST', 'localhost');

define('SITE_HOME', dirname(__FILE__));

header('Content-type: text/html; charset=utf-8');
session_start();

include_once 'fw/public_functions.php';
incDir('fw/core/');