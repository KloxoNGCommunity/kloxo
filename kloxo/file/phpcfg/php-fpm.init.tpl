<?php
	$phpdesc_fpm = (isset($phpdesc)) ? $phpdesc . "-fpm" : 'php-fpm';
?>

#! /bin/sh
#
# chkconfig: - 84 16
# description:  PHP FastCGI Process Manager
# processname: php-fpm
# config: /etc/php-fpm.conf
# pidfile: /var/run/php-fpm/php-fpm.pid

# Standard LSB functions
#. /lib/lsb/init-functions

# Source function library.
. /etc/init.d/functions

# Check that networking is up.
. /etc/sysconfig/network

if [ "$NETWORKING" = "no" ]
then
    exit 0
fi

RETVAL=0
prog="<?=$phpdesc_fpm;?>"
pidfile=${PIDFILE-/var/run/php-fpm/<?=$phpdesc_fpm;?>.pid}
lockfile=${LOCKFILE-/var/lock/subsys/<?=$phpdesc_fpm;?>}

start () {
    echo -n $"Starting $prog: "
    daemon --pidfile ${pidfile} <?=$phpdesc_fpm;?>

    RETVAL=$?
    echo
    [ $RETVAL -eq 0 ] && touch ${lockfile}
}
stop () {
    echo -n $"Stopping $prog: "
    killproc -p ${pidfile} <?=$phpdesc_fpm;?>

    RETVAL=$?
    echo
    if [ $RETVAL -eq 0 ] ; then
        rm -f ${lockfile} ${pidfile}
    fi
}

restart () {
    stop
    start
}

reload () {
    echo -n $"Reloading $prog: "
    killproc -p ${pidfile} <?=$phpdesc_fpm;?> -USR2
    RETVAL=$?
    echo
}


# See how we were called.
case "$1" in
  start)
    start
    ;;
  stop)
    stop
    ;;
  status)
    status -p ${pidfile} <?=$phpdesc_fpm;?>

    RETVAL=$?
    ;;
  restart)
    restart
    ;;
  reload|force-reload)
    reload
    ;;
  condrestart|try-restart)
    [ -f ${lockfile} ] && restart || :
    ;;
  *)
    echo $"Usage: $0 {start|stop|status|restart|reload|force-reload|condrestart|try-restart}"
    RETVAL=2
        ;;
esac

exit $RETVAL
