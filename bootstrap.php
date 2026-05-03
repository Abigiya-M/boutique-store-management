<?php
/**
 * Bootstrap File - Initializes the application
 */

// Define base path constants
if (!defined('APP_PATH')) {
    define('APP_PATH', dirname(__DIR__) . '/app');
}
if (!defined('CONFIG_PATH')) {
    define('CONFIG_PATH', dirname(__DIR__) . '/config');
}
if (!defined('STORAGE_PATH')) {
    define('STORAGE_PATH', dirname(__DIR__) . '/storage');
}
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}
if (!defined('PUBLIC_PATH')) {
    define('PUBLIC_PATH', ROOT_PATH . '/public');
}

// Load logging helper
require_once APP_PATH . '/helpers/logging.php';

// Load environment configuration
require_once CONFIG_PATH . '/config.php';

// Load exception classes
require_once APP_PATH . '/Exceptions/AppException.php';
require_once APP_PATH . '/Exceptions/HttpException.php';
require_once APP_PATH . '/Exceptions/NotFoundException.php';

// Load core framework classes
require_once APP_PATH . '/core/Database.php';
require_once APP_PATH . '/core/Model.php';
require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/core/Router.php';

// Load vendor autoloader
require_once ROOT_PATH . '/vendor/autoload.php';

// Set timezone
date_default_timezone_set(APP_TIMEZONE ?? 'UTC');

return [
    'app_path' => APP_PATH,
    'config_path' => CONFIG_PATH,
    'storage_path' => STORAGE_PATH,
    'root_path' => ROOT_PATH,
];
