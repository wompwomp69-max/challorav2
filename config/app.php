<?php
/**
 * App config
 */
session_start();

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('BASE_URL', '/challorav2/public');
define('STORAGE_CV', BASE_PATH . '/storage/cv');

$autoloadPath = BASE_PATH . '/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require $autoloadPath;
}

spl_autoload_register(function ($class) {
    $paths = [
        BASE_PATH . '/core/',
        APP_PATH . '/controllers/',
        APP_PATH . '/models/',
    ];
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});

require BASE_PATH . '/config/database.php';
require BASE_PATH . '/core/helpers.php';
