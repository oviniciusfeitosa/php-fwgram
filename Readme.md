# php-fwgram

### About
It is an easy-to-use tool created using PHP and other open-source technologies to provide basic integration actions with the Instagram API.

**Attention use this tool at your own risk.**

### Goals
- [x] Backup instagram Following list(string, json)
- [x] Backup instagram Followers list(string, json)
- [x] Like by Hashtag 
    - [x] Filtering by:
        - [x] One like Per User
        - [x] Show liked users at end
        - [x] Gender 
- [ ] Follow :
    - [x] Create a JSON file with a list of Followed Users if doesn't exists.
    - [x] Filters: 
        - [x] By User 
        - [x] By List of Users 
        - [x] By Hashtag
        - [ ] Check if user follows me
- [x] Unfollow :
    - [x] Filters:  
        - [x] By JSON file with a list of Followed Users
        - [x] By JSON file with a following backup list
        - [ ] Massively (All Users)
- [ ] Comments :
    - @todo: Fill here
- [ ] Add Tests

### Steps

1. Download Application dependencies.
```
    composer update
```

2. Create your own ```.env``` file by  ```.env.example```

3. Setup your ```APP_USERNAME``` and ```APP_PASSWORD```

4. Choose one of actions inside ```actions``` folder and execute using PHP.

You will find some actions below. 

### Action : Like by Hashtags

You will need to set following environment variables in your ```.env``` file:
- USERNAME_SEARCHED
- HASHTAG
- BACKUP_DATA_FOLDER
- MAXIMUM_LIKES_PER_HASHTAG
- MAXIMUM_LIKES
- ONE_LIKE_PER_USER
- LIKE_MALE
- LIKE_FEMALE
- SHOW_LIKED_USERS

To perform this action, execute the command bellow.
```
php actions/likeByHashtag.php
```

### Action : Follow by Hashtags

You will need to set following environment variables in your ```.env``` file:
- USERNAME_SEARCHED
- HASHTAG
- BACKUP_DATA_FOLDER
- MAXIMUM_FOLLOWED_PER_HASHTAG
- MAXIMUM_FOLLOWED
- SHOW_FOLLOWED_USERS

To perform this action, execute the command bellow.
```
php actions/followByHashtag.php
```

### Action : Unfollow 

You will need to set following environment variables in your ```.env``` file:
- USERNAME_SEARCHED
- HASHTAG
- BACKUP_DATA_FOLDER
- MAXIMUM_FOLLOWED
- SHOW_FOLLOWED_USERS

To perform this action, execute the command bellow.
```
php actions/unfollowByJsonFile.php
```

or 
```
php unfollowByFollowingBackup.php
```

### Action : Backup User Following

You will need to set following environment variables in your ```.env``` file:
- BACKUP_DATA_FOLDER
- USERNAME_SEARCHED

To perform this action, execute the command bellow.
```
php actions/backupFollowing.php
```

### Action : Backup User Followers

You will need to set following environment variables in your ```.env``` file:
- BACKUP_DATA_FOLDER
- USERNAME_SEARCHED

To perform this action, execute the command bellow.
```
php actions/backupFollowers.php
```

## Tecnologies
- PHP 7+
- Docker

## References
- https://github.com/mgp25/Instagram-API/wiki
