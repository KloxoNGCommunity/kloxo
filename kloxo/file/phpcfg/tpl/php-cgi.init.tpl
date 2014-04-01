<?php
	$phpdesc_cgi = (isset($phpdesc)) ? $phpdesc . "-cgi" : 'php-cgi';
?>
#!/bin/sh
#
# php-cgi - php-fastcgi swaping via  spawn-fcgi
#
# chkconfig:   - 85 15
# description:  Run php-cgi as app server
# processname: php-cgi
# config:      /etc/sysconfig/phpfastcgi (defaults RH style)
# pidfile:     /var/run/php_cgi.pid
# Note: See how to use this script :
# http://www.cyberciti.biz/faq/rhel-fedora-install-configure-nginx-php5/
# Source function library.
. /etc/rc.d/init.d/functions
 
# Source networking configuration.
. /etc/sysconfig/network
 
# Check that networking is up.
[ "$NETWORKING" = "no" ] && exit 0
 
spawnfcgi="/usr/bin/spawn-fcgi"
php_cgi="/usr/bin/<?=$phpdesc_cgi;?>"
prog=$(basename $php_cgi)
server_ip=127.0.0.1
server_port=9000
server_socket="/tmp/$php_cgi"

server_user=<?=$user;?>

server_group=<?=$user;?>

pidfile="/var/run/<?=$phpdesc_cgi;?>"
 
[ -f /etc/sysconfig/<?=$phpdesc_cgi;?> ] && . /etc/sysconfig/<?=$phpdesc_cgi;?>

 
start() {
    [ -x $php_cgi ] || exit 1
    [ -x $spawnfcgi ] || exit 2
    echo -n $"Starting $prog: "
    daemon $spawnfcgi -s ${server_socket} \
        -u ${server_user} -g ${server_group} -P ${pidfile} \
        -C ${server_childs} -f ${php_cgi}
    retval=$?
    echo
    return $retval
}
 
stop() {
    echo -n $"Stopping $prog: "
    killproc -p ${pidfile} $prog -QUIT
    retval=$?
    echo
    [ -f ${pidfile} ] && /bin/rm -f ${pidfile}
    return $retval
}
 
restart(){
	stop
	sleep 2
	start
}
 
rh_status(){
	status -p ${pidfile} $prog
}
 
case "$1" in
    start)
        start;;
    stop)
        stop;;
    restart)
        restart;;
    status)
        rh_status;;
    *)
        echo $"Usage: $0 {start|stop|restart|status}"
        exit 3
esac
