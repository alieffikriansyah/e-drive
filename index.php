<?php

/**
 * E-Drive — Enterprise Document Management System
 * Front Controller (index.php)
 * 
 * Satu-satunya file yang dipanggil pertama kali oleh web server.
 * Semua request dibelokkan ke sini via .htaccess.
 */

// ─── Error Reporting ───
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ─── Define Root Paths ───
define('ROOTPATH', __DIR__ . DIRECTORY_SEPARATOR);
define('APPPATH', ROOTPATH . 'app' . DIRECTORY_SEPARATOR);
define('MODULESPATH', ROOTPATH . 'modules' . DIRECTORY_SEPARATOR);
define('STORAGEPATH', ROOTPATH . 'storage' . DIRECTORY_SEPARATOR);
define('UPLOADPATH', ROOTPATH . 'upload' . DIRECTORY_SEPARATOR);

// ─── Load Core Classes ───
require_once APPPATH . 'core/Env.php';
require_once APPPATH . 'core/Session.php';
require_once APPPATH . 'core/Security.php';
require_once APPPATH . 'core/Database.php';
require_once APPPATH . 'core/Model.php';
require_once APPPATH . 'core/Loader.php';
require_once APPPATH . 'core/Controller.php';
require_once APPPATH . 'core/Router.php';

// ─── Load Environment Variables ───
Env::load(ROOTPATH . '.env');

// ─── Error Display Based on Environment ───
if (Env::get('APP_ENV') === 'production') {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// ─── Start Session ───
Session::init();

// ─── Load Helpers ───
require_once APPPATH . 'helpers/url_helper.php';
require_once APPPATH . 'helpers/form_helper.php';
require_once APPPATH . 'helpers/log_helper.php';

// ─── Load Configuration ───
require_once APPPATH . 'config/config.php';

// ─── Dispatch Router ───
$router = new Router();
$router->dispatch();
