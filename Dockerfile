# cd webphp
# vi Dockerfile
FROM php:7.4-apache
# Install Mysql
RUN docker-php-ext-install mysqli pdo_mysql
#RUN apt-get update\
#    && apt-get install -y php7.0-mysql\
#    && docker-php-ext-install mysqli pdo_mysql
COPY app/ /var/www/html
#RUN chmod -R 777 /var/www/html/
#RUN chmod -R 777 /var/www/html/system/libraries/
USER root 
RUN chmod -R ugo+rwx /var/www/html/
#ENTRYPOINT ["bash", "-c", "chmod -R 777 /var/www/html/"]
EXPOSE 80