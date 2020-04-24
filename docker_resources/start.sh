#!/bin/bash
service nginx start
service mysql start
service php5-fpm start
service sphinxsearch start
/etc/init.d/rsyslog start
/etc/init.d/cron start
cron -f
