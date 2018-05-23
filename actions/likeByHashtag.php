<?php

try {

    require __DIR__ . '/../config.php';

    $hashtagsString = getenv('HASHTAG');

    if(is_null($hashtagsString) || empty($hashtagsString)) {
        throw new Exception("You need to define `HASHTAG` in .env file");
    }

    $hashTags = explode('|', $hashtagsString);
    print "\n=== [ {$sleepingTime} Likes - Start! ] ===\n";

    $maximumLikes = getenv('MAXIMUM_LIKES');
    if(is_null($maximumLikes) || empty($maximumLikes)) {
        $maximumLikes = 90;
    }
    foreach($hashTags as $hashTag) {

        $rankToken = \InstagramAPI\Signatures::generateUUID();

        $maxId = null;
        $likeCount = 0;
        print "\n=== [ Hashtag: #{$hashtag} ] ===\n";
        do {
            $response = $ig->hashtag->getFeed($hashtag, $rankToken, $maxId);

            foreach ($response->getItems() as $item) {
                $instagramAPI->media->like($item->getMedia()->getId());
//                $instagramAPI->people->get($item->getUser()->getPk());
                $likeCount++;
                echo "Like number: [ {$likeCount} ]\n";
                $sleepingTime = rand(1,2);
                echo "Sleeping for {$sleepingTime}s...\n";
                sleep($sleepingTime);
            }

            $maxId = $response->getNextMaxId();

            $sleepingTimeNextPage = rand(2,5);
            echo "Changing to next page -> Sleeping for {$sleepingTimeNextPage}s...\n";
            sleep($sleepingTimeNextPage);

        } while ($maxId !== null || $likeCount !== $maximumLikes);
    }

    $instagramAPI->logout();

    print "\n=== [ {$likeCount}/{$maximumLikes} Likes - Complete! ] ===\n";

} catch (\Exception $e) {
    echo "Something went wrong: {$e->getMessage()}\n";
}

