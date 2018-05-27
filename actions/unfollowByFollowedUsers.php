<?php

try {

    require __DIR__ . '/../config.php';

    $backupDataFolder = getenv('BACKUP_DATA_FOLDER');
    if(is_null($backupDataFolder) || empty($backupDataFolder)) {
        throw new Exception("You need to define `BACKUP_DATA_FOLDER` in .env file");
    }

    $userFollowedFilePath = "{$backupDataFolder}/{$username}_followed.json";
    if (!is_file($userFollowedFilePath)) {
        throw new Exception("Followed Json file does not exists.");
    }

    $userFollowedJson = file_get_contents($userFollowedFilePath);
    $followedUsers = json_decode($userFollowedJson, true);

    print "\n*** [ INFO ] ***\n";
    print "\n*** [ Users Followed - Start] ***\n";
    print_r($followedUsers);
    print "\n*** [ Users Followed - End] ***\n";

    print "\n=== [ Unfollow By JsonFile - Start! ] ===\n";


    foreach ($followedUsers as $followedUser) {
        print "\n*** [ Unfollow - User: @{$followedUser} ] ***\n";
        $info = $instagramAPI->people->getInfoByName($followedUser);
        $instagramAPI->people->unfollow($info->getUser()->getPk());
        $sleepingTimeNextPage = rand(5, 7);

        echo "** Moving to next user -> Sleeping for {$sleepingTimeNextPage}s... **\n";
        sleep($sleepingTimeNextPage);
    }

    $instagramAPI->logout();
    file_put_contents($userFollowedFilePath, json_encode([]));
    print "\n=== [ Unfollow By JsonFile - Done! ] ===\n";
} catch (\Exception $e) {
    echo "Something went wrong: {$e->getMessage()}\n";
}

