# php-fwgram

## Goals
- [ ] Generate instagram Following list
- [ ] Follow friend or list of Friends
- [ ] Unfollow Massively

### Steps

Download Application dependencies.
```
    composer update
```

Run the command below to create a secure connection to instagram access your api.

You may replace **9999** by port wich you want.
```
docker run --rm -it wernight/ngrok ngrok http localhost:9999
```

```
php -S 0.0.0.0:9999 -t public public/index.php
```

if you are using any other network service with apache or nginx you may use like this:
```
docker run --rm -it --link web_service_container wernight/ngrok ngrok http web_service_container:80
```

## Tecnologies
- PHP 7+
- Docker
- Ngrok

## References
- https://github.com/wernight/docker-ngrok
- https://www.sitepoint.com/conquering-instagram-with-php-and-the-instagram-api
