<?php

if (defined('ROOT')) {
    echo __FILE__, ' is executed twice!', "\n";
    exit(1);
}

define('START_TIME', microtime(true));
define('ROOT', __DIR__);
date_default_timezone_set('UTC');
ini_set('display_errors', 'On');
error_reporting(E_ALL);
mb_internal_encoding('utf-8');
ini_set('mysql.connect_timeout', 1000);
ini_set('default_socket_timeout', 1000);
ini_set('session.cookie_lifetime', $_ = (3600 * 24 * 365));
ini_set('session.gc_maxlifetime', $_);

if (!is_file($_ = ROOT . '/vendor/autoload.php')) {
    echo "No autoload.php! Use Composer to install all dependencies!\n";
    exit(1);
} else {
    require $_;
}

define('LOGS_ROOT', ROOT . '/logs');
define('TPL_ROOT', ROOT . '/tpl');
define('DOTENV_FILE', ROOT . '/.env');
define('CONSTANTS_FILE', ROOT . '/constants.php');
define('CONTAINER_FILE', ROOT . '/container.php');

if (!is_file(DOTENV_FILE)) {
    echo "Please, set correctly your environment file!\n";
    exit(1);
}

set_include_path(get_include_path() . PATH_SEPARATOR . TPL_ROOT);

(new Dotenv\Dotenv(dirname(DOTENV_FILE), basename(DOTENV_FILE)))->load();

if (!getenv('ENV')) {
    echo "ENV is not set!\n";
    exit(1);
}

foreach (['dev', 'stage', 'production'] as $env) {
    define('IS_' . strtoupper($env), getenv('ENV') === $env);
}

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\ErrorHandler;
use Monolog\Formatter\LineFormatter;

if (class_exists('Logger')) {
    $level = constant('\Monolog\Logger::' . (getenv('LEVEL') ?: 'DEBUG'));
    if (!is_dir(LOGS_ROOT)) mkdir(LOGS_ROOT);
    $file = LOGS_ROOT . '/' . date('Y-m-d') . '.log';
    $handlers = [new StreamHandler($file, $level)];
    if (defined('STDIN'))
        $handlers[] = new StreamHandler('php://stderr', $level);
    $logger = new Logger('general', $handlers);
    $handler = new ErrorHandler($logger);
    $handler->registerErrorHandler([], false);
    $handler->registerExceptionHandler(null, false);
    $handler->registerFatalHandler();
}

if (is_file(CONSTANTS_FILE)) {
    require CONSTANTS_FILE;
}

$argv = $_SERVER['argv'] ?? [];

if (defined('STDIN') and realpath($argv[0]) === realpath(__FILE__)) {
    array_shift($argv);
    if (!$argv) exit(1);
    $func = array_shift($argv);
    $res = $func(...$argv);
    if (is_bool($res))
        exit($res ? 0 : 1);
    if (is_int($res))
        $res .= '';
    if (is_string($res))
        echo trim($res), "\n";
    exit(0);
}
