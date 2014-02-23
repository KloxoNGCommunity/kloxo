<?php
    $userinfo = posix_getpwnam($user);

    if ($user === 'apache') {
        // MR -- for future purpose, apache user have uid 50000
        $fpmport = 50000;
        $openbasedir = "/home/kloxo/httpd/:/tmp/:/usr/share/pear/:/var/lib/php/session/";
    } else {
        $userinfo = posix_getpwnam($user);
        $fpmport = (50000 + $userinfo['uid']);
        $openbasedir = "/home/$user/:/tmp/:/usr/share/pear/:/var/lib/php/session/:".
		"/home/kloxo/httpd/script/:/home/kloxo/httpd/disable/";
    }

    if ($user == 'apache') {
        $pool = 'default';
    } else {
        $pool = $user;
    }

    if ($maxchildren) {
        $startservers = (($sts = (int)($maxchildren / 3 * 2)) < 2) ? 2 : $sts;
        $minspareservers = (($mis = (int)($maxchildren / 3)) < 2) ? 2 : $mis;
        $maxspareservers = (($mas = (int)($maxchildren / 3 * 2)) < 2) ? 2 : $mas;
        $maxchildren = (($mac = (int)($maxchildren)) < 2) ? 2 : $mac;
    } else {
        $startservers = '4';
        $minspareservers = '2';
        $maxspareservers = '4';
        $maxchildren = '6';
    }
?>
[<?php echo $pool; ?>]
;listen = 127.0.0.1:<?php echo $fpmport; ?>

listen = /home/php-fpm/sock/<?php echo $user; ?>.sock
listen.backlog = 65536
listen.allowed_clients = 127.0.0.1
user = <?php echo $user; ?>

group = <?php echo $user; ?>

;pm = dynamic
pm = ondemand
pm.max_children = <?php echo $maxchildren; ?>

;pm.start_servers = <?php echo $startservers; ?>

;pm.min_spare_servers = <?php echo $minspareservers; ?>

;pm.max_spare_servers = <?php echo $maxspareservers; ?>

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
security.limit_extensions = .php .php3 .php4 .php5

env[HOSTNAME] = $HOSTNAME
env[PATH] = /usr/local/bin:/usr/bin:/bin
env[TMP] = /tmp
env[TMPDIR] = /tmp
env[TEMP] = /tmp

;php_admin_value[sendmail_path] = /usr/sbin/sendmail -t -i -f webmaster@domain.com
;php_flag[display_errors] = off
php_admin_value[error_log] = /var/log/php-fpm/error.log
php_admin_value[session.save_path] = /var/lib/php/session
php_admin_flag[log_errors] = on
;php_admin_value[memory_limit] = 64M
php_admin_value[open_basedir] = <?php echo $openbasedir; ?>

;php_admin_value[session.save_path] = /home/<?php echo $user; ?>

