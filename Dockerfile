FROM ubuntu:16.04

LABEL maintainer "kunze@earthlinginteractive.com"

RUN apt-get update && apt-get install -y python-software-properties wget curl git zip nano vim netcat supervisor cron rsyslog apache2 php php-cli libapache2-mod-php php-mysql php-dom php-simplexml php-curl php-intl php-xsl php-mbstring php-zip php-xml php-xmlrpc composer php-gd php-mcrypt php-redis mysql-client && a2enmod rewrite remoteip; phpenmod mcrypt;

RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar && \
    mv wp-cli.phar /usr/local/bin/wp && \
    chmod +x /usr/local/bin/wp;

ADD wp-completion.bash /etc/profile.d/wp-completion.bash
ADD 000-default.conf /etc/apache2/sites-enabled/000-default.conf
ADD httpd-foreground /bin/httpd-foreground
ADD entrypoint.sh /entrypoint.sh

RUN echo "source /etc/profile.d/wp-completion.bash" >> /root/.bash_profile

RUN sed -i \
    -e 's/upload_max_filesize = [0-9]\+M/upload_max_filesize = 100M/g' \
    -e 's/post_max_size = [0-9]\+M/post_max_size = 110M/g' \
    -e 's/memory_limit = [0-9]\+M/memory_limit = 1024M/g' \
    -e 's/; max_input_vars = [0-9]\+/max_input_vars = 100000/g' \
    -e 's/error_reporting = E_ALL \& ~E_DEPRECATED \& ~E_STRICT/error_reporting = E_ALL \& ~E_DEPRECATED \& ~E_STRICT \& ~E_NOTICE \& ~E_WARNING/g' \
    /etc/php/7.0/apache2/php.ini;

EXPOSE 80

CMD ["httpd-foreground"]
