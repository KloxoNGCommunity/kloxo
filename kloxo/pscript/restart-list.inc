list_kloxo=(kloxo-web kloxo-php kloxo-wrap)

#if [ "$(command -v systemctl)" != "" ] ; then
if [ "$(ps --no-headers -o comm 1)" == "systemd" ] ; then
	#list_phpfpm=($(ls -ld /usr/lib/systemd/system/php*-fpm.service|awk '{print $9}'|sed 's:/usr/lib/systemd/system/::g'|sed 's:.service::g'|tr '\n' ' '))
	list_phpfpm=($(systemctl list-unit-files --type=service|awk '{print $1}'|sed 's/\.service//g'|grep ^'php'|grep '\-fpm'))
else
	#list_phpfpm=($(ls -ld /etc/rc.d/init.d/php*-fpm|awk '{print $9}'|sed 's:/etc/rc.d/init.d/::g'|tr '\n' ' '))
	list_phpfpm=($(chkconfig --list|grep ':on'|grep ^'php'|grep '\-fpm'|awk '{print $1}'|tr '\n' ' '))
fi

list_web=(varnish trafficserver squid spawn-fcgi memcached lighttpd nginx hiawatha httpd htcacheclean)
list_web=("${list_phpfpm[@]}" "${list_web[@]}")

list_mysql=(mysql mysqld)
list_mail=(qmail)
list_ftp=(pure-ftpd)
list_dns=(named djbdns pdns nsd yadifad)
list_syslog=(syslog rsyslog syslog-ng httpry)

list_services=("${list_mysql[@]}" "${list_dns[@]}" "${list_web[@]}" "${list_mail[@]}" "${list_ftp[@]}")

list_all=("${list_syslog[@]}" "${list_services[@]}" "${list_kloxo[@]}")
