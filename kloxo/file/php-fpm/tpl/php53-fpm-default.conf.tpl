<?php
	if (!$phpselected) {
		$phpcli = 'php';
		$phpselected = 'php';
	} else {
		if ($phpselected === 'php') {
			$phpcli = "php";
		} else {
			$phpcli = "{$phpselected}-cli";
		}
	}
?>
[<?=$phpselected;?>-default]
; listen = 127.0.0.1:50000
listen = /opt/configs/php-fpm/sock/<?=$phpselected;?>-apache.sock
listen.backlog = -1
listen.allowed_clients = 127.0.0.1
listen.backlog = 65536
listen.owner = apache
listen.group = apache
listen.mode = 0666
user = apache
group = apache
pm = ondemand
pm.max_children = 6
pm.start_servers = 2
pm.min_spare_servers = 2
pm.max_spare_servers = 4
pm.max_requests = 1000
;pm.status_path = /status
;ping.path = /ping
;ping.response = pong
request_terminate_timeout = 120s
request_slowlog_timeout = 30s
slowlog = /var/log/php-fpm/slow.log
rlimit_files = 1024
rlimit_core = 0
;chroot = 
;chdir = /var/www
catch_workers_output = yes
security.limit_extensions = .php .php5 .php7

env[HOSTNAME] = $HOSTNAME
env[PATH] = /bin:/usr/bin:/sbin:/usr/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin
env[TMP] = /tmp
env[TMPDIR] = /tmp
env[TEMP] = /tmp

;php_admin_value[sendmail_path] = /usr/sbin/sendmail -t -i -f webmaster@domain.com
;php_flag[display_errors] = off
php_admin_value[error_log] = /var/log/php-fpm/error.log
php_admin_value[session.save_path] = /var/lib/php/session
php_admin_flag[log_errors] = on
php_admin_value[memory_limit] = 128M
