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

    $isLikeMale = (bool)getenv('LIKE_MALE');
    if (is_null($isLikeMale)) {
        throw new Exception("You need to define `LIKE_MALE` in .env file");
    }

    $isLikeFemale = (bool)getenv('LIKE_FEMALE');
    if (is_null($isLikeFemale)) {
        throw new Exception("You need to define `LIKE_FEMALE` in .env file");
    }

    $showLikedUsers = (bool)getenv('SHOW_LIKED_USERS');
    if (is_null($oneLikePerUser)) {
        throw new Exception("You need to define `SHOW_LIKED_USERS` in .env file");
    }

    $likedMediaPath = "{$backupDataFolder}/{$username}_liked_media.json";
    if (!is_file($likedMediaPath)) {
        file_put_contents($likedMediaPath, json_encode([]));
    }

    $likedMediaJson = file_get_contents($likedMediaPath);
    $likedMedia = json_decode($likedMediaJson, true);

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

                $mediaId = $item->getId();
                if (is_null($mediaId)) {
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

                if(in_array($mediaId, $likedMedia)) {
                    echo "** Already Liked Media **\n";
                    continue;
                }

                $instagramAPI->media->like($mediaId);
                $likedUsers[] = $mediaUsernameToLike;
                $likedMedia[] = $mediaId;

                $hashTaglikeCounter++;
                $likeCount++;
                echo "=> Like number: [ {$likeCount} ]\n";

                $sleepingTime = rand(4, 7);
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

            $sleepingTimeNextPage = rand(4, 8);
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

file_put_contents($likedMediaPath, json_encode($likedMedia));