FROM ubuntu:14.04
ENV DEBIAN_FRONTEND noninteractive
#RUN echo "nameserver 192.168.99.1" > /etc/resolv.conf ; \
RUN \
    sed -i "s#archive.ubuntu.com#cn.archive.ubuntu.com#" /etc/apt/sources.list ; \
    apt-get update; \
    echo 'mysql-server mysql-server/root_password password kf_kf_kf' | debconf-set-selections  ; \
    echo 'mysql-server mysql-server/root_password_again password kf_kf_kf' | debconf-set-selections ;\
    apt-get install -y nginx php5-fpm php5-mysqlnd php5-curl php5-gd mysql-server mysql-client rsyslog ; \
    service mysql start && echo 'create database knowledge;GRANT ALL PRIVILEGES ON knowledge.* TO "klg_u"@"%" IDENTIFIED BY "magic*docker";flush privileges;'| mysql -uroot -p'kf_kf_kf' ; \
    useradd -d /var/www/carbon_forum web; \
    mkdir -p /var/www/carbon_forum

RUN ps -ef|grep mysql


# cd /tmp;\
# wget https://pecl.php.net/get/sphinx-1.3.3.tgz ;\
# tar xf sphinx-1.3.3.tgz && cd sphinx-1.3.3 ; \
# phpize && ./configure && make && make install;\
# rm /tmp/*; \

COPY docker_resources/sphinx.so /usr/lib/php5/20121212/

#RUN echo "nameserver 192.168.99.1" > /etc/resolv.conf ;
RUN \ 
        apt-get install curl;\
        apt-get install -y sphinxsearch  libsphinxclient-0.0.1 sphinxbase-utils ;\
        sed -i "s/START=no/START=yes/" /etc/default/sphinxsearch; \
        echo "extension=sphinx.so" > /etc/php5/mods-available/sphinx.ini ;\
        ln -sv /etc/php5/mods-available/sphinx.ini  /etc/php5/fpm/conf.d/30-sphinx.ini;\
        service mysql start  && echo 'GRANT ALL PRIVILEGES ON knowledge.* TO "sphinx_u"@"%" IDENTIFIED BY "search_perfect";flush privileges;'| mysql -uroot -p'kf_kf_kf' ; \
        echo '*/5 * * * *  /usr/bin/indexer --config /etc/sphinxsearch/sphinx.conf --all --rotate >/dev/null 2>&1' |crontab

ADD docker_resources/sphinx.conf /etc/sphinxsearch/sphinx.conf


COPY docker_resources/nginx_default /etc/nginx/sites-enabled/default
COPY docker_resources/start.sh /
RUN chmod +x /start.sh
RUN cat /etc/hosts
RUN ps -ef|grep mysql

ADD . /var/www/carbon_forum/
RUN cp /usr/share/zoneinfo/Asia/Shanghai /etc/localtime
RUN \
    service mysql start; \
    mysql -uroot -p'kf_kf_kf' knowledge < /var/www/carbon_forum/install/database.sql
RUN chown  -R www-data /var/www/carbon_forum ; rm -rf /var/www/carbon_forum/{docker_resources,Dockerfile}
RUN \
    service mysql start; \
    /usr/bin/indexer --config /etc/sphinxsearch/sphinx.conf --all --rotate;
ENTRYPOINT ["/start.sh"]
WORKDIR /var/www/carbon_forum


#18 4 * * *  /usr/local/bin/indexer --config /data/sphinx/sphinx.conf --all --rotate >/dev/null 2>&1

