<?php
define('APP_NAME',    'AgruKrwanda');
define('APP_VERSION', '1.0.0');
$_appScheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
$_appHost   = $_SERVER['HTTP_HOST'] ?? 'localhost';
$_appFolder = trim(str_replace($_SERVER['DOCUMENT_ROOT'] ?? '', '', dirname(dirname(__FILE__))), '/');
define('APP_URL', $_appScheme . '://' . $_appHost . '/' . $_appFolder);
unset($_appScheme, $_appHost, $_appFolder);
define('BASE_PATH',   dirname(__DIR__));
define('PUBLIC_PATH', BASE_PATH . '/public');
define('UPLOAD_PATH', PUBLIC_PATH . '/assets/uploads');
define('LOG_PATH',    BASE_PATH . '/logs');
define('CURRENCY',    'RWF');

ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Strict');

error_reporting(E_ALL);
ini_set('display_errors', 1);

date_default_timezone_set('Africa/Kigali');

spl_autoload_register(function ($class) {
    $paths = [
        BASE_PATH . '/app/models/',
        BASE_PATH . '/app/controllers/',
        BASE_PATH . '/app/services/',
        BASE_PATH . '/app/helpers/',
        BASE_PATH . '/app/middleware/',
        BASE_PATH . '/config/',
        BASE_PATH . '/routes/',
    ];
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) { require_once $file; return; }
    }
});

require_once BASE_PATH . '/config/database.php';
