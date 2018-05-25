<?php

try {

    require __DIR__ . '/../config.php';

    $hashtagsConcatened = getenv('HASHTAG');

    if (is_null($hashtagsConcatened) || empty($hashtagsConcatened)) {
        throw new Exception("You need to define `HASHTAG` in .env file");
    }

    $maximumLikes = (int)getenv('MAXIMUM_LIKES');
    if (is_null($maximumLikes) || empty($maximumLikes) || $maximumLikes < 1) {
        throw new Exception("You need to define `MAXIMUM_LIKES` in .env file");
    }

    $maximumLikesPerHashtag = (int)getenv('MAXIMUM_LIKES_PER_HASHTAG');
    if (is_null($maximumLikesPerHashtag) || empty($maximumLikesPerHashtag) || $maximumLikesPerHashtag < 1) {
        throw new Exception("You need to define `MAXIMUM_LIKES_PER_HASHTAG` in .env file");
    }

    $oneLikePerUser = (bool)getenv('ONE_LIKE_PER_USER');
    if (is_null($oneLikePerUser)) {
        throw new Exception("You need to define `ONE_LIKE_PER_USER` in .env file");
    }

    if (is_null($oneLikePerUser)) {
        throw new Exception("You need to define `SHOW_LIKED_USERS` in .env file");
    }

    $hashTagsArray = explode('|', $hashtagsConcatened);
    $hashTags = "#" . implode(' #', $hashTagsArray);

    print "\n=== [ Like By Hashtag [user: {$username} | Hashtags: {$hashTags}] - Start! ] ===\n";

    $likeCount = 0;
    $likedUsers = [];

    foreach ($hashTagsArray as $hashTag) {
        $hashTaglikeCounter = 0;
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

            echo "itens number: " . sizeof($items) . "\n";

            foreach ($items as $item) {
                $itemId = $item->getId();

                if (is_null($itemId)) {
                    echo "Without media to like. \n";
                    continue;
                }

                $usernameToLike = $item->getUser()->getUsername();

                if($oneLikePerUser === true && in_array($usernameToLike, $likedUsers)) {
                    echo "Username {$usernameToLike} already had a liked media. \n";
                    continue;
                }

                $instagramAPI->media->like($itemId);
                array_push($likedUsers, $usernameToLike);

                $hashTaglikeCounter++;
                $likeCount++;
                echo "Like number: [ {$likeCount} ]\n";

                $sleepingTime = rand(1, 2);
                echo "Sleeping for {$sleepingTime}s...\n";
                sleep($sleepingTime);

                if ($maximumLikesPerHashtag == $hashTaglikeCounter) {
                    break;
                }

                if ($likeCount == $maximumLikes) {
                    break;
                }
            }

            if ($hashTaglikeCounter == $maximumLikesPerHashtag) {
                echo "** Maximum likes reached for #{$hashTag} [ {$hashTaglikeCounter} / {$maximumLikesPerHashtag} ] **\n";
                break;
            }

            if ($likeCount == $maximumLikes) {
                break;
            }

            $maxId = $response->getNextMaxId();

            $sleepingTimeNextPage = rand(2, 5);
            echo "** Changing to next page -> Sleeping for {$sleepingTimeNextPage}s... **\n";
            sleep($sleepingTimeNextPage);

        } while ($maxId !== null);

        if ($likeCount == $maximumLikes) {
            echo "** Maximum likes reached [ {$likeCount} / {$maximumLikes} ] **\n";
            break;
        }
    }

    $instagramAPI->logout();

    print "\n=== [ {$likeCount}/{$maximumLikes} Likes for Hashtags: {$hashTags} - Complete! ] ===\n";

    $showLikedUsers = (bool)getenv('SHOW_LIKED_USERS');

    if($showLikedUsers === true) {
        print "\n=== [ Users Liked - Start] ===\n";
        print_r($likedUsers);
        print "\n=== [ Users Liked - End] ===\n";
    }

} catch (\Exception $e) {
    echo "Something went wrong: {$e->getMessage()}\n";
}

