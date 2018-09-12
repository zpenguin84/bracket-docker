FROM ubuntu:18.04

RUN apt-get update && apt-get install -y php7.2-cli
ADD ./ /application/
WORKDIR /application/
CMD php app.php
