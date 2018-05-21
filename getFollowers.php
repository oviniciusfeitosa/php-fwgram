<?php

ini_set('display_errors', true);
error_reporting(E_ALL);

require 'vendor/autoload.php';

$debug = false;
$truncatedDebug = true;

$instagramAPI = new \InstagramAPI\Instagram($debug, $truncatedDebug, [
    'storage' => 'sqlite',
    'dbhost' => 'localhost',
    'dbname' => 'mydatabase',
    'dbusername' => 'root',
    'dbpassword' => '',
]);

$username = 'xxx';
$password = 'xxx';
try {
    $instagramAPI->login($username, $password);
} catch (\Exception $e) {
    echo 'Something went wrong: ' . $e->getMessage() . "\n";
    exit(0);
}

$userNameSearched = 'vinnyfs89';
$info = $instagramAPI->people->getInfoByName($userNameSearched);
file_put_contents("data/{$userNameSearched}_infos.json", json_encode($info));
file_put_contents("data/{$userNameSearched}_infos.txt", print_r($info, true));

try {
    $followers = [
        'users' => [],
        'count' => 0
    ];

    $maxId = null;
    $rankToken = \InstagramAPI\Signatures::generateUUID();
    $searchQuery = null;
    do {

        $response = $instagramAPI->people->getFollowers(
            $info->getUser()->getPk(),
            $rankToken,
            $searchQuery,
            $maxId);
        $apiUsers = $response->getUsers();
        foreach ($apiUsers as $key => $userModel) {
            /**
             * @var $userModel \InstagramAPI\Response\Model\User
             */
            end($followers['users']);
            $userIndex = (int)key($followers['users']) + 1;
            reset($followers['users']);

            $followers['users'][$userIndex]['pk'] = $userModel->getPk();
            $followers['users'][$userIndex]['username'] = $userModel->getUsername();
            $followers['users'][$userIndex]['full_name'] = $userModel->getFullName();
        }
        $followers['count'] += count($apiUsers);
        $followers['nextMaxId'] = $response->getNextMaxId();
        $maxId = $followers['nextMaxId'];
    } while ($maxId !== null);

} catch (\Exception $e) {
    echo 'Something went wrong: ' . $e->getMessage() . "\n";
}

file_put_contents("data/{$userNameSearched}_followers.json", json_encode($followers));
file_put_contents("data/{$userNameSearched}_followers.txt", print_r($followers, true));

$instagramAPI->logout();
//
//if (PHP_SAPI === 'cli-server' && $_SERVER['SCRIPT_FILENAME'] !== __FILE__) {
//    return false;
//}
//
//require __DIR__ . '/../vendor/autoload.php';
//session_start();
//
//$settings = require __DIR__ . '/../app/settings.php';
//$app = new \Slim\App($settings);
//
//require __DIR__ . '/../app/dependencies.php';
//require __DIR__ . '/../app/middleware.php';
//require __DIR__ . '/../app/routes.php';
//
//$app->run();