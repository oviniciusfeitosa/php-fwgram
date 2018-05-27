<?php

try {

    require __DIR__ . '/../config.php';

    $hashtagsConcatened = getenv('HASHTAG');

    if (is_null($hashtagsConcatened) || empty($hashtagsConcatened)) {
        throw new Exception("You need to define `HASHTAG` in .env file");
    }

    $showLikedUsers = (bool)getenv('SHOW_LIKED_USERS');
    if (is_null($oneLikePerUser)) {
        throw new Exception("You need to define `SHOW_LIKED_USERS` in .env file");
    }

    define('GENDER_MALE', (int)1);
    define('GENDER_FEMALE', (int)0);

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

            echo "=> Media numbers: " . sizeof($items) . "\n";

            foreach ($items as $item) {

                $itemId = $item->getId();
                if (is_null($itemId)) {
                    echo "** Without media to like. **\n";
                    continue;
                }

                $mediaUsernameToLike = $item->getUser()->getUsername();
                if ($oneLikePerUser === true && in_array($mediaUsernameToLike, $likedUsers)) {
                    echo "** Username [{$mediaUsernameToLike}] already had a liked media. **\n";
                    continue;
                }

                $mediaUserGender = $item->getUser()->getGender();
                if (
                    !is_null($mediaUserGender)
                    and
                    ($isLikeMale === true && $mediaUserGender !== GENDER_MALE)
                    and
                    ($isLikeFemale === true && $mediaUserGender !== GENDER_FEMALE)
                ) {
                    echo "** Gender does not mismatch for user [{$mediaUsernameToLike}]. **\n";
                    continue;
                }

                $instagramAPI->media->like($itemId);
                array_push($likedUsers, $mediaUsernameToLike);

                $hashTaglikeCounter++;
                $likeCount++;
                echo "=> Like number: [ {$likeCount} ]\n";

                $sleepingTime = rand(1, 2);
                echo "=> Sleeping for {$sleepingTime}s...\n";
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

    if ($showLikedUsers === true) {
        print "\n=== [ Users Liked - Start] ===\n";
        print_r($likedUsers);
        print "\n=== [ Users Liked - End] ===\n";
    }

} catch (\Exception $e) {
    echo "Something went wrong: {$e->getMessage()}\n";
}

