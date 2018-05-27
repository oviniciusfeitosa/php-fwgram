<?php

try {

    require __DIR__ . '/../config.php';

    $backupDataFolder = getenv('BACKUP_DATA_FOLDER');
    if(is_null($backupDataFolder) || empty($backupDataFolder)) {
        throw new Exception("You need to define `BACKUP_DATA_FOLDER` in .env file");
    }

    $userFollowingFilePath = __DIR__ . "/../{$backupDataFolder}/{$username}_following.json";
    if (!is_file($userFollowingFilePath)) {
        throw new Exception("Followed Json file does not exists.");
    }

    $userFollowingJson = file_get_contents($userFollowingFilePath);
    $followingUsers = json_decode($userFollowingJson, true);

    $usersFollowingBackup = [];
    foreach($followingUsers['users'] as $user) {
        $usersFollowingBackup[] = $user['username'];
    }

    print "\n*** [ INFO ] ***\n";
    print "\n*** [ Users Following Backup - Start] ***\n";
    print_r($usersFollowingBackup);
    print "\n*** [ Users Following Backup - End] ***\n";

    print "\n=== [ Unfollow By Following Backup file - Start! ] ===\n";

    $info = $instagramAPI->people->getInfoByName($username);
    $maxId = null;
    $rankToken = \InstagramAPI\Signatures::generateUUID();
    $searchQuery = null;

    $followedUsers = [];

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
                print "\n*** [ Found Followed User @{$userModel->getUsername()} - By Diff ] ***\n";
                $followedUsers[] = $userModel->getUsername();

                $sleepingTimeNextPage = rand(5, 7);

            }
        }
        echo "** Moving to next user -> Sleeping for {$sleepingTimeNextPage}s... **\n";
        sleep($sleepingTimeNextPage);
        $maxId = $response->getNextMaxId();
    } while ($maxId !== null);

    $instagramAPI->logout();

    print "\n=== [ Unfollow By Following Backup file - Done! ] ===\n";
} catch (\Exception $e) {
    echo "Something went wrong: {$e->getMessage()}\n";
}
file_put_contents("{$backupDataFolder}/{$username}_followed.json", json_encode($followedUsers));

