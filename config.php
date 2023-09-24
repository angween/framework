<?php

if(
    (empty($_SERVER['SERVER_NAME']) && strpos(PHP_SAPI, 'cgi') !== 0) || 
    (!empty($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] == 'localhost')
){
    if ( ! $webINI = parse_ini_file("env_dev.ini", true) ) {
        die('File ENV tidak ditemukan!');
    }
} else {
    if ( ! $webINI = parse_ini_file("env_prod.ini", true) ) {
        die('File ENV tidak ditemukan!!');
    }
}

$__root        = (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'];
$__script_name = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

define('DS', DIRECTORY_SEPARATOR);

define('PATH_URI', $__root . $__script_name);                  /** http://localhost/mvc/ */
define('PATH_FILE', __DIR__ . DS );                            /** C:\xampp\htdocs\mvc\ */

define('DEBUG',           $webINI['app']['DEBUG']);

<<<<<<< HEAD
define('APP_NAME', $webINI['app']['NAME']);
define('APP_EMAIL',$webINI['app']['EMAIL']);
define('APP_DESC ',$webINI['app']['EMAIL']);
define('KEYWORDS', $webINI['app']['KEYWORDS']);
define('HASH_COST', $webINI['app']['HASH_COST']);
=======
define('APP_NAME',        $webINI['app']['NAME']);
define('APP_EMAIL',       $webINI['app']['EMAIL']);
define('APP_DESC ',       $webINI['app']['EMAIL']);
define('KEYWORDS',        $webINI['app']['KEYWORDS']);
define('HASH_COST',       $webINI['app']['HASH_COST']);
>>>>>>> ff26cff91f0880e78cc994c74abb010fdf45f626

define('COMPANY_NAME', $webINI['company']['NAME']);
define('COMPANY_ADDRESS', $webINI['company']['ADDRESS']);
define('COMPANY_PHONE', $webINI['company']['PHONE']);

<<<<<<< HEAD
define('DB_HOSTNAME', $webINI['database']['HOSTNAME']);
define('DB_USERNAME', $webINI['database']['USERNAME']);
define('DB_PASSWORD', $webINI['database']['PASSWORD']);
define('DB_DATABASE', $webINI['database']['DATABASE']);
define('DB_PORT',$webINI['database']['PORT']);

error_reporting(DEBUG);
date_default_timezone_set($webINI['database']['TIMEZONE']);
=======
define('PROGRAM_PATH',    $webINI['company']['PROGRAM_PATH']);
define('DB_HOSTNAME',     $webINI['database']['HOSTNAME']);
define('DB_USERNAME',     $webINI['database']['USERNAME']);
define('DB_PASSWORD',     $webINI['database']['PASSWORD']);
define('DB_DATABASE',     $webINI['database']['DATABASE']);
define('DB_PORT',         $webINI['database']['PORT']);
>>>>>>> ff26cff91f0880e78cc994c74abb010fdf45f626
