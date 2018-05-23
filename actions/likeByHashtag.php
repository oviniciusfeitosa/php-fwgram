<?php

try {

    require __DIR__ . '/../config.php';

    $hashtagsString = getenv('HASHTAG');

    if(is_null($hashtagsString) || empty($hashtagsString)) {
        throw new Exception("You need to define `HASHTAG` in .env file");
    }

    $hashTags = explode('|', $hashtagsString);
    print "\n=== [ {$sleepingTime} Likes - Start! ] ===\n";
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
            }

            $maxId = $response->getNextMaxId();

            $sleepingTime = rand(3,5);
            echo "Sleeping for {$sleepingTime}s...\n";
            sleep($sleepingTime);

        } while ($maxId !== null);
    }

    $instagramAPI->logout();

    print "\n=== [ {$sleepingTime} Likes - Complete! ] ===\n";

} catch (\Exception $e) {
    echo 'Something went wrong: ' . $e->getMessage() . "\n";
}

