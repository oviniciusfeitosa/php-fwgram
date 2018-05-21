# php-fwgram

## Goals
- [x] Generate instagram Following list(string, json)
- [x] Generate instagram Followers list(string, json)
- [ ] Follow friend or list of Friends
- [ ] Unfollow Massively

### Steps

Download Application dependencies.
```
    composer update
```

Run the commands below to get:

- User Following
```
php getFollowing.php
```

- User Follower
```
php getFollowers.php
```

if you are using any other network service with apache or nginx you may use like this:
```
docker run --rm -it --link web_service_container wernight/ngrok ngrok http web_service_container:80
```

## Tecnologies
- PHP 7+
- Docker