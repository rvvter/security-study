#!/bin/sh
echo ServerName localhost:80 >> /etc/apache2/apache2.conf
/etc/init.d/apache2 start
rm -f /var/www/html/index.html
find /var/lib/mysql -type f -exec touch {} \; && /etc/init.d/mysql start
chown -R mysql:mysql /var/run/mysqld/
mysql -e "grant all privileges on *.* to 'root'@'%' identified by 'root';"
mysql -e "grant all privileges on *.* to 'root'@'localhost' identified by 'root';"
mysql -uroot -proot -e "create database zhifu;"
mysql -uroot -proot -e "set names utf8;"
mysql -uroot -proot -D "zhifu" -e "source /zhifu.sql;"

sleep infinity