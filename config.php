<?php

ini_set('display_errors', true);
error_reporting(E_ALL);

require 'vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$debug = false;
$truncatedDebug = true;

$databaseData = [];
if(getenv('DB_DRIVER')
    && getenv('DB_HOST')
    && getenv('DB_DATABASE')
    && getenv('DB_USERNAME')
    && getenv('DB_PASSWORD')) {
    $databaseData = [
        'storage' => getenv('DB_DRIVER'),
        'dbhost' => getenv('DB_HOST'),
        'dbname' => getenv('DB_DATABASE'),
        'dbusername' => getenv('DB_USERNAME'),
        'dbpassword' => getenv('DB_PASSWORD')
    ];
}

$instagramAPI = new \InstagramAPI\Instagram($debug, $truncatedDebug, $databaseData);

$username = getenv('APP_USERNAME');
$password = getenv('APP_PASSWORD');
try {
    if(is_null($username) || empty($username)) {
        throw new Exception("You need to define `APP_USERNAME` in .env file");
    }

    if(is_null($password) || empty($password)) {
        throw new Exception("You need to define `APP_PASSWORD` in .env file");
    }
    $instagramAPI->login($username, $password);
} catch (\Exception $e) {
    echo 'Something went wrong: ' . $e->getMessage() . "\n";
    exit(0);
}

