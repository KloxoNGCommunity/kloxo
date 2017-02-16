list_web=(varnish trafficserver squid php-fpm phpm-fpm spawn-fcgi memcached lighttpd nginx hiawatha httpd htcacheclean)
list_syslog=(syslog rsyslog syslog-ng httpry)
list_services=(mysqld mysql named djbdns pdns nsd mydns yadifad varnish trafficserver squid httpd nginx lighttpd hiawatha qmail pure-ftpd php-fpm phpm-fpm)
list_spawncgi=(spawn-fcgi)
list_mysql=(mysql mysqld)
list_mail=(qmail)
list_ftp=(pure-ftpd)
list_dns=(named djbdns pdns nsd mydns yadifad)
list_all=(syslog rsyslog syslog-ng httpry mysql mysqld named djbdns pdns nsd mydns yadifad varnish trafficserver squid php-fpm phpm-fpm spawn-fcgi memcached lighttpd nginx hiawatha httpd htcacheclean qmail pure-ftpd kloxo)
list_kloxo=(kloxo-web kloxo-php)

if [ "$(command -v systemctl)" != "" ] ; then
	list_phpfpm=$(ls -ld /usr/lib/systemd/system/php*-fpm.service | awk '{print $9}' | sed 's:/usr/lib/systemd/system/::g' | sed 's:/.service/::g' | tr '\n' ' ')
else
	#list_phpfpm=$(ls -ld /etc/rc.d/init.d/php*-fpm | awk '{print $9}' | sed 's:/etc/rc.d/init.d/::g' | tr '\n' ' ')
	list_phpfpm=(php-fpm phpm-fpm)
fi

