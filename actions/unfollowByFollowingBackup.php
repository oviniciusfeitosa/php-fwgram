<?php

try {

    require __DIR__ . '/../config.php';

    $backupDataFolder = getenv('BACKUP_DATA_FOLDER');
    if(is_null($backupDataFolder) || empty($backupDataFolder)) {
        throw new Exception("You need to define `BACKUP_DATA_FOLDER` in .env file");
    }

    $userFollowedFilePath = __DIR__ . "/../{$backupDataFolder}/{$username}_following.json";
    if (!is_file($userFollowedFilePath)) {
        throw new Exception("Followed Json file does not exists.");
    }

    $userFollowingJson = file_get_contents($userFollowedFilePath);
    $followingUsers = json_decode($userFollowingJson, true);

    $usersFollowingBackup = [];
    foreach($followingUsers['users'] as $user) {
        $usersFollowingBackup[] = $user['usename'];
    }

    print "\n*** [ INFO ] ***\n";
    print "\n*** [ Users Followed - Start] ***\n";
    print_r($usersFollowingBackup);
    print "\n*** [ Users Followed - End] ***\n";

    print "\n=== [ Unfollow By Following Backup file - Start! ] ===\n";

    $info = $instagramAPI->people->getInfoByName($username);
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

            if(!in_array($userModel->getUsername(), $usersFollowingBackup)) {
                print "\n*** [ Unfollow - User: @{$followingUser} ] ***\n";
                $instagramAPI->people->unfollow($userModel->getPk());

                $sleepingTimeNextPage = rand(7, 9);

                echo "** Moving to next user -> Sleeping for {$sleepingTimeNextPage}s... **\n";
                sleep($sleepingTimeNextPage);
            }


        }
        $maxId = $following['nextMaxId'];
    } while ($maxId !== null);

    $instagramAPI->logout();

    print "\n=== [ Unfollow By Following Backup file - Done! ] ===\n";
} catch (\Exception $e) {
    echo "Something went wrong: {$e->getMessage()}\n";
}

