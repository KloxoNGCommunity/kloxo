list_kloxo=(kloxo-web kloxo-php kloxo-wrap)

if [ "$(command -v systemctl)" != "" ] ; then
	list_phpfpm=($(ls -ld /usr/lib/systemd/system/php*-fpm.service | awk '{print $9}' | sed 's:/usr/lib/systemd/system/::g' | sed 's:/.service/::g' | tr '\n' ' '))
else
	list_phpfpm=($(ls -ld /etc/rc.d/init.d/php*-fpm | awk '{print $9}' | sed 's:/etc/rc.d/init.d/::g' | tr '\n' ' '))
fi

list_web=(varnish trafficserver squid spawn-fcgi memcached lighttpd nginx hiawatha httpd htcacheclean)
list_web=("${list_phpfpm[@]}" "${list_web[@]}")

list_mysql=(mysql mysqld)
list_mail=(qmail)
list_ftp=(pure-ftpd)
list_dns=(named djbdns pdns nsd mydns yadifad)
list_syslog=(syslog rsyslog syslog-ng httpry)

list_services=("${list_mysql[@]}" "${list_dns[@]}" "${list_web[@]}" "${list_mail[@]}" "${list_ftp[@]}")

list_all=("${list_syslog[@]}" "${list_services[@]}" "${list_web[@]}" "${list_kloxo[@]}")
