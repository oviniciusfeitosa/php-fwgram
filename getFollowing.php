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
    $following = [
        'users' => [],
        'count' => 0
    ];

    $maxId = null;
    $rankToken = \InstagramAPI\Signatures::generateUUID();
    $searchQuery = null;
    do {

        $response = $instagramAPI->people->getFollowing(
            $info->getUser()->getPk(),
            $rankToken,
            $searchQuery,
            $maxId
        );
        $apiUsers = $response->getUsers();
        foreach ($apiUsers as $key => $userModel) {
            /**
             * @var $userModel \InstagramAPI\Response\Model\User
             */
            end($following['users']);
            $userIndex = (int)key($following['users']) + 1;
            reset($following['users']);

            $following['users'][$userIndex]['pk'] = $userModel->getPk();
            $following['users'][$userIndex]['username'] = $userModel->getUsername();
            $following['users'][$userIndex]['full_name'] = $userModel->getFullName();
        }
        $following['count'] += count($apiUsers);
        $following['nextMaxId'] = $response->getNextMaxId();
        $maxId = $following['nextMaxId'];
    } while ($maxId !== null);

} catch (\Exception $e) {
    echo 'Something went wrong: ' . $e->getMessage() . "\n";
}

file_put_contents("data/{$userNameSearched}_following.json", json_encode($following));
file_put_contents("data/{$userNameSearched}_following.txt", print_r($following, true));

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