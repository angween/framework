<?php
declare(strict_types=1);

use core\controller\{App, Router};

require_once 'vendor/autoload.php';
error_reporting(E_ALL);
date_default_timezone_set('Asia/Jakarta');

/*********************
 * Globals variable for the app
 *********************/
$__root        = (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'];
$__script_name = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
$__CSRF_match  = FALSE;
$__LOCALHOST   = $_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['SERVER_ADDR'] === '127.0.0.1' || str_contains($_SERVER['HTTP_HOST'],'45.45.45');


/*********************
 * Folder and path
 *********************/
define('APP_OWNER', 'core');
define('IS_LOCALHOST', $__LOCALHOST);
define('DS', DIRECTORY_SEPARATOR );
define('CLASS_CORE', APP_OWNER . '\controller');
define('PATH_CORE', 'app' . DS . APP_OWNER . DS );						                /** app/core/ */
define('PATH_DIR', $__script_name);												        /** framework */
define('URL_ROOT', $__root . $__script_name); 									        /** http://localhost/framework/ */
define('URL_CORE_VIEW', $__root . $__script_name . 'app/core/view/' );                  /** http://localhost/framework/app/core/view/ */
define('URL_PUBLIC_VIEW', $__root . $__script_name . 'view/' );                         /** http://localhost/framework/view/ */
define('PATH_FILE', realpath($_SERVER['DOCUMENT_ROOT'] . DS . PATH_DIR . DS ));         /** C:\xampp\htdocs\framework\ */
define('PATH_FILE_VIEW',  'app' .DS. 'core' .DS. 'view' .DS. '' );                      /** C:\xampp\htdocs\framework\app\core\view\ */
define('ID_TIMELENGTH', 8 );													        /** Panjang timestamp yg dipakai oleh semua ID (product, vendor dll) */
// define('SESSION_NAME', 'RLAawesomeSistem' );


/****************
 * Start engine
 ****************/
require("controller/App.php");


/*********************
 * Update harian/bulanan/tahunan
 *********************/

// set_error_handler( 'App::errorHandler', E_ALL );
require("controller/Router.php");
