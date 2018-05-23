<?php

try {

    require __DIR__ . '/../config.php';

    $hashtagsConcatened = getenv('HASHTAG');

    if (is_null($hashtagsConcatened) || empty($hashtagsConcatened)) {
        throw new Exception("You need to define `HASHTAG` in .env file");
    }

    $hashTagsArray = explode('|', $hashtagsConcatened);
    $hashTags = "#" . implode(' #', $hashTagsArray);

    print "\n=== [ Like By Hashtag [user: {$username} | Hashtags: {$hashTags}] - Start! ] ===\n";

    $maximumLikes = (int) getenv('MAXIMUM_LIKES');
    if (is_null($maximumLikes) || empty($maximumLikes) || $maximumLikes < 1) {
        $maximumLikes = 90;
    }
    $likeCount = 0;
    foreach ($hashTagsArray as $hashTag) {

        $rankToken = \InstagramAPI\Signatures::generateUUID();

        $maxId = null;
        print "\n=== [ Hashtag: #{$hashTag} ] ===\n";
        do {
            $response = $instagramAPI->hashtag->getFeed($hashTag, $rankToken, $maxId);
            if($response->isItems()) {
                $items = $response->getItems();
            } else {
                $items = $response->getRankedItems();
            }

            echo "itens number: " . sizeof($items) . "\n";

            foreach ($items as $item) {
                $itemId = $item->getId();

                if (is_null($itemId)) {
                    echo "Without media to like. \n";
                    continue;
                }

                $instagramAPI->media->like($itemId);
//                $instagramAPI->people->get($item->getUser()->getPk());

                $likeCount++;
                echo "Like number: [ {$likeCount} ]\n";

                $sleepingTime = rand(1, 2);
                echo "Sleeping for {$sleepingTime}s...\n";
                sleep($sleepingTime);
                if( $likeCount == $maximumLikes) {
                    echo "Maximum likes reached [ {$maximumLikes} ]\n";
                    break;
                }
            }

            if( $likeCount == $maximumLikes) {
                break;
            }

            $maxId = $response->getNextMaxId();

            $sleepingTimeNextPage = rand(2, 5);
            echo "Changing to next page -> Sleeping for {$sleepingTimeNextPage}s...\n";
            sleep($sleepingTimeNextPage);

        } while ($maxId !== null);

        if( $likeCount == $maximumLikes) {
            break;
        }
    }

    $instagramAPI->logout();

    print "\n=== [ {$likeCount}/{$maximumLikes} Likes for Hashtags: {$hashTags} - Complete! ] ===\n";

} catch (\Exception $e) {
    echo "Something went wrong: {$e->getMessage()}\n";
}

