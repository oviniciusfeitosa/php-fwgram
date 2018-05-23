[![Build Status](https://travis-ci.com/vinnyfs89/php-fwgram.svg?branch=master)](https://travis-ci.com/vinnyfs89/php-fwgram)

# php-fwgram

## Goals
- [x] Backup instagram Following list(string, json)
- [x] Backup instagram Followers list(string, json)
- [x] Like by Hashtag 
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

4. (Optional) If you want, you can store data defining the environments below:
```
DB_DRIVER="mysql"
DB_HOST="localhost"
DB_USERNAME="admin"
DB_PASSWORD="admin"
DB_DATABASE="fwgram"
```

5. (Optional) If you want you can execute the ```docker-compose up -d``` command to have a mysql database running. 

### Store User Following

- You need to set ```USERNAME_SEARCHED="xxxx"``` in your ```.env``` file.

Execute the command bellow.
```
php actions/backupFollowing.php
```

### Store User Followers

- You need to set ```USERNAME_SEARCHED="xxxx"``` in your ```.env``` file.

Execute the command bellow.
```
php actions/backupFollowers.php
```

## Tecnologies
- PHP 7+
- Docker

## References
- https://github.com/mgp25/Instagram-API/wiki
