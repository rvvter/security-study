echo ServerName localhost:80 >> /etc/apache2/apache2.conf
/etc/init.d/apache2 start
rm -f /var/www/html/index.html

sleep infinity