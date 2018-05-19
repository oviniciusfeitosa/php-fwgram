<?php

ini_set('display_errors', true);
error_reporting(E_ALL);

require '../vendor/autoload.php';

if (PHP_SAPI === 'cli-server' && $_SERVER['SCRIPT_FILENAME'] !== __FILE__) {
    return false;
}

require __DIR__ . '/../vendor/autoload.php';
session_start();

$settings = require __DIR__ . '/../app/settings.php';
$app = new \Slim\App($settings);

require __DIR__ . '/../app/dependencies.php';
require __DIR__ . '/../app/middleware.php';
require __DIR__ . '/../app/routes.php';

$app->run();