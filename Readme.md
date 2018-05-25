# php-fwgram

### About
It is an easy-to-use tool created using PHP and other open-source technologies to provide basic integration actions with the Instagram API.

### Goals
- [x] Backup instagram Following list(string, json)
- [x] Backup instagram Followers list(string, json)
- [x] Like by Hashtag 
- [x] Like by Hashtag - Filtering by:
    - [x] One like Per User
    - [x] Show liked users at end
    - [x] Gender 
    - [ ] Age - Above 18 years. 
- [ ] Follow friend 
- [ ] Follow list of Friends
- [ ] Follow by Hashtag
- [ ] Unfollow Massively
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
- MAXIMUM_LIKES_PER_HASHTAG
- MAXIMUM_LIKES
- ONE_LIKE_PER_USER
- LIKE_MALE=true
- LIKE_FEMALE=true
- SHOW_LIKED_USERS

To perform this action, execute the command bellow.
```
php actions/likeByHashtag.php
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
