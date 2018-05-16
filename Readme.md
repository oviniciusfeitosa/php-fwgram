# php-fwgram

### About

### Steps

Run the command below to create a secure connection to instagram access your api.

You may replace **9999** by port wich you want.
```
docker run --rm -it wernight/ngrok ngrok http localhost:9999
```

if you are using any other network service with apache or nginx you may use like this:
```
docker run --rm -it --link web_service_container wernight/ngrok ngrok http web_service_container:80
```
