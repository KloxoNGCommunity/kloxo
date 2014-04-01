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

    if (!$phpdesc) {
        $phpdesc = '5.3.0';
    }

    if (version_compare($phpver, "5.4.0", ">=")) {
        $php54disable = ';';
    } else {
        $php54disable = '';
    }
?>
[<?=$pool;?>]
;catch_workers_output = yes
;listen = 127.0.0.1:<?=$fpmport;?>

listen = /home/php-fpm/sock/<?=$user;?>.sock
listen.backlog = 65536
listen.allowed_clients = 127.0.0.1
user = <?=$user;?>

group = <?=$user;?>

;pm = dynamic
pm = ondemand
pm.max_children = <?=$maxchildren;?>

;pm.start_servers = <?=$startservers;?>

;pm.min_spare_servers = <?=$minspareservers;?>

;pm.max_spare_servers = <?=$maxspareservers;?>

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

php_admin_value[zlib.output_compression] = <?=$output_compression_flag;?>

php_admin_value[disable_functions] = <?=$disable_functions;?>

php_admin_value[display_errors] = <?=$display_error_flag;?>

php_admin_value[file_uploads] = <?=$file_uploads_flag;?>

php_admin_value[fiupload_max_filesize] = <?=$upload_max_filesize;?>

php_admin_value[log_errors] = <?=$log_errors_flag;?>

php_admin_value[output_buffering] = <?=$output_buffering_flag;?>

php_admin_value[register_argc_argv] = <?=$register_argc_argv_flag;?>

<?=$php54disable;?>php_admin_value[magic_quotes_gpc] = <?=$magic_quotes_gpc_flag;?>

php_admin_value[post_max_size] = <?=$post_max_size_flag;?>

<?=$php54disable;?>php_admin_value[magic_quotes_runtime] = <?=$magic_quotes_runtime_flag;?>

php_admin_value[mysql.allow_persistent] = <?=$mysql_allow_persistent_flag;?>

php_admin_value[max_execution_time] = <?=$max_execution_time_flag;?>

php_admin_value[max_input_time] = <?=$max_input_time_flag;?>

php_admin_value[memory_limit] = <?=$memory_limit_flag;?>

php_admin_value[post_max_size] = <?=$post_max_size_flag;?>

php_admin_value[allow_url_fopen] = <?=$allow_url_fopen_flag;?>

php_admin_value[allow_url_include] = <?=$allow_url_include_flag;?>

php_admin_value[session.save_path] = <?=$session_save_path_flag;?>

php_admin_value[cgi.force_redirect] = <?=$cgi_force_redirect_flag;?>

<?=$php54disable;?>php_admin_value[safe_mode] = <?=$safe_mode_flag;?>

php_admin_value[enable_dl] = <?=$enable_dl_flag;?>

php_admin_value[open_basedir] = <?=$openbasedir;?>
