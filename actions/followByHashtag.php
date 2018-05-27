<?php

try {

    require __DIR__ . '/../config.php';

    $hashtagsConcatened = getenv('HASHTAG');

    if (is_null($hashtagsConcatened) || empty($hashtagsConcatened)) {
        throw new Exception("You need to define `HASHTAG` in .env file");
    }

    $maximumfollowed = (int)getenv('MAXIMUM_FOLLOWED');
    if (is_null($maximumfollowed) || empty($maximumfollowed) || $maximumfollowed < 1) {
        throw new Exception("You need to define `MAXIMUM_FOLLOWED` in .env file");
    }

    $maximumfollowedPerHashtag = (int)getenv('MAXIMUM_FOLLOWED_PER_HASHTAG');
    if (is_null($maximumfollowedPerHashtag) || empty($maximumfollowedPerHashtag) || $maximumfollowedPerHashtag < 1) {
        throw new Exception("You need to define `MAXIMUM_FOLLOWED_PER_HASHTAG` in .env file");
    }

    $showFollowdUsers = (bool)getenv('SHOW_FOLLOWED_USERS');
    if (is_null($showFollowdUsers)) {
        throw new Exception("You need to define `SHOW_FOLLOWED_USERS` in .env file");
    }

    $backupDataFolder = getenv('BACKUP_DATA_FOLDER');
    if(is_null($backupDataFolder) || empty($backupDataFolder)) {
        throw new Exception("You need to define `BACKUP_DATA_FOLDER` in .env file");
    }

    $userFollowedFilePath = "{$backupDataFolder}/{$username}_followed.json";
    if (!is_file($userFollowedFilePath)) {
        file_put_contents($userFollowedFilePath, json_encode([]));
    }

    $userFollowedJson = file_get_contents($userFollowedFilePath);
    $followedUsers = json_decode($userFollowedJson, true);

    $hashTagsArray = explode('|', $hashtagsConcatened);
    $hashTags = "#" . implode(' #', $hashTagsArray);

    print "\n*** [ INFO ] ***\n";
    print "\n*** [ Maximum Followed: {$maximumfollowed} ] ***\n";
    print "\n*** [ Maximum Followed Per Hashtag: {$maximumfollowedPerHashtag} ] ***\n";
    print "\n*** [ / INFO ] ***\n";
    print "\n=== [ Follow By Hashtag [user: {$username} | Hashtags: {$hashTags}] - Start! ] ===\n";

    $followCount = 0;

    foreach ($hashTagsArray as $hashTag) {
        $hashTagUserFollowedCounter = 0;
        $rankToken = \InstagramAPI\Signatures::generateUUID();
        $maxId = null;
        print "\n=== [ Hashtag: #{$hashTag} ] ===\n";
        do {
            $response = $instagramAPI->hashtag->getFeed($hashTag, $rankToken, $maxId);
            if ($response->isItems()) {
                $items = $response->getItems();
            } else {
                $items = $response->getRankedItems();
            }

            echo "=> Media numbers: " . sizeof($items) . "\n";

            foreach ($items as $item) {

                $itemId = $item->getId();
                if (is_null($itemId)) {
                    echo "** Without media to follow user. **\n";
                    continue;
                }

                $user = $item->getUser();
                $mediaUsernameToFollow = $user->getUsername();

                if (in_array($mediaUsernameToFollow, $followedUsers)) {
                    echo "** Username [{$mediaUsernameToFollow}] already was followed. **\n";
                    continue;
                }
                $instagramAPI->people->follow($user->getPk());
                $followedUsers[] = $mediaUsernameToFollow;

                $hashTagUserFollowedCounter++;
                $followCount++;
                echo "=> Follow number: [ {$followCount} ]\n";

                $sleepingTime = rand(4, 6);
                echo "=> Sleeping for {$sleepingTime}s...\n";
                sleep($sleepingTime);

                if ($maximumfollowedPerHashtag == $hashTagUserFollowedCounter) {
                    break;
                }

                if ($followCount == $maximumfollowed) {
                    break;
                }
            }

            if ($hashTagUserFollowedCounter == $maximumfollowedPerHashtag) {
                echo "** Maximum followed reached for #{$hashTag} [ {$hashTagUserFollowedCounter} / {$maximumfollowedPerHashtag} ] **\n";
                break;
            }

            if ($followCount == $maximumfollowed) {
                break;
            }

            $maxId = $response->getNextMaxId();

            $sleepingTimeNextPage = rand(2, 5);
            echo "** Changing to next page -> Sleeping for {$sleepingTimeNextPage}s... **\n";
            sleep($sleepingTimeNextPage);

        } while ($maxId !== null);

        if ($followCount == $maximumfollowed) {
            echo "** Maximum followed reached [ {$followCount} / {$maximumfollowed} ] **\n";
            break;
        }
    }

    $instagramAPI->logout();


    print "\n=== [ {$followCount}/{$maximumfollowed} followed for Hashtags: {$hashTags} - Complete! ] ===\n";

    if ($showFollowdUsers === true) {
        print "\n=== [ Users Followed - Start] ===\n";
        print_r($followedUsers);
        print "\n=== [ Users Followed - End] ===\n";
    }

} catch (\Exception $e) {
    echo "Something went wrong: {$e->getMessage()}\n";
}

file_put_contents($userFollowedFilePath, json_encode($followedUsers));
