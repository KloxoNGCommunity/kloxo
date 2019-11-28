<?php
	if (!file_exists("/opt/configs/php-fpm/sock")) {
		mkdir("/opt/configs/php-fpm/sock");
	}

	if (!file_exists("/var/run/php-fpm")) {
		mkdir("/var/run/php-fpm");
	}

	$userinfo = posix_getpwnam($user);

	if ($user === 'apache') {
		// MR -- for future purpose, apache user have uid 50000
		$fpmport = 50000;
		$openbasedir = "/home/:/tmp/:/usr/share/pear/:/var/lib/php/session/";
	} else {
		$userinfo = posix_getpwnam($user);
		$fpmport = (50000 + $userinfo['uid']);
		$openbasedir = "/home/$user/:/tmp/:/usr/share/pear/:/var/lib/php/session/:".
			"/home/kloxo/httpd/script/:/home/kloxo/httpd/disable/:{$extrabasedir}";
	}

	if ($user === 'apache') {
		$pool = 'default';
	} else {
		$pool = $user;
	}

	if ($maxchildren) {
		$startservers = (($sts = (int)($maxchildren / 3)) < 2) ? 2 : $sts;
		$minspareservers = (($mis = (int)($maxchildren / 6)) < 2) ? 2 : $mis;
		$maxspareservers = (($mas = (int)($maxchildren / 3)) < 2) ? 2 : $mas;
		$maxchildren = (($mac = (int)($maxchildren)) < 2) ? 2 : $mac;
	} else {
		$startservers = '4';
		$minspareservers = '2';
		$maxspareservers = '4';
		$maxchildren = '6';
	}

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

//	exec("php -r 'echo phpversion();'", $out, $ret);
	exec("{$phpcli} -v|grep 'PHP'|grep '(built:'|awk '{print $2}'", $out, $ret);

	if ($ret) {
		$phpver = '5.4.0';
	} else {
		$phpver = $out[0];
	}

	if (version_compare($phpver, "5.4.0", ">=")) {
		$php54disable = ';';
	} else {
		$php54disable = '';
	}

	if (!$max_input_vars_flag) {
		$max_input_vars_flag = '3000';
	}

	if (!$date_timezone_flag) {
		$date_timezone_flag = 'Europe/London';
	}

	if (!$phpfpm_type_flag) {
		$phpfpm_type_flag = 'ondemand';
	}

	if ($user === 'apache') {
		$chroot_dir = "/home/kloxo/httpd";
		$enable_chroot = ";";
	} else {
		$chroot_dir = "/home/{$user}";
		$enable_chroot = "";
	}

	$openbasedir = str_replace("/var/lib/php/session/", "{$session_save_path_flag}/", $openbasedir);
?>
[<?=$phpselected;?>-<?=$pool;?>]
;listen = 127.0.0.1:<?=$fpmport;?>

listen = /opt/configs/php-fpm/sock/<?=$phpselected;?>-<?=$user;?>.sock
listen.backlog = 65536
listen.allowed_clients = 127.0.0.1
listen.owner = <?=$user;?>

listen.group = <?=$user;?>

listen.mode = 0666
user = <?=$user;?>

group = <?=$user;?>

pm = <?=$phpfpm_type_flag;?>

pm.max_children = <?=$maxchildren;?>

pm.start_servers = <?=$startservers;?>

pm.min_spare_servers = <?=$minspareservers;?>

pm.max_spare_servers = <?=$maxspareservers;?>

pm.max_requests = 1000
pm.process_idle_timeout = 10s

;pm.status_path = /status
;ping.path = /ping
;ping.response = pong
request_terminate_timeout = <?=$max_execution_time_flag;?>s
request_slowlog_timeout = 30s
slowlog = /var/log/php-fpm/slow.log
rlimit_files = 10240
rlimit_core = 0
;<?=$enable_chroot;?>chroot = <?=$chroot_dir;?>

;chdir = /
catch_workers_output = yes
security.limit_extensions = <?=$phpfpm_limit_extensions;?>


env[HOSTNAME] = $HOSTNAME
env[PATH] = /bin:/usr/bin:/sbin:/usr/sbin:/usr/local/bin
env[TMP] = /tmp
env[TMPDIR] = /tmp
env[TEMP] = /tmp
env[OSTYPE] = $OSTYPE
env[MACHTYPE] = $MACHTYPE
env[MALLOC_CHECK_] = 2

php_flag[zlib.output_compression] = <?=$output_compression_flag;?>

php_admin_value[disable_functions] = <?=$disable_functions;?>

php_flag[display_errors] = <?=$display_error_flag;?>

php_flag[file_uploads] = <?=$file_uploads_flag;?>

php_admin_value[upload_max_filesize] = <?=$upload_max_filesize;?>

php_flag[log_errors] = <?=$log_errors_flag;?>

php_flag[output_buffering] = <?=$output_buffering_flag;?>

php_flag[register_argc_argv] = <?=$register_argc_argv_flag;?>

php_admin_value[max_execution_time] = <?=$max_execution_time_flag;?>

php_admin_value[max_input_time] = <?=$max_input_time_flag;?>

php_admin_value[memory_limit] = <?=$memory_limit_flag;?>

php_admin_value[post_max_size] = <?=$post_max_size_flag;?>

php_flag[allow_url_fopen] = <?=$allow_url_fopen_flag;?>

php_flag[allow_url_include] = <?=$allow_url_include_flag;?>

php_admin_value[session.save_path] = <?=$session_save_path_flag;?>

php_flag[cgi.force_redirect] = <?=$cgi_force_redirect_flag;?>

php_flag[enable_dl] = <?=$enable_dl_flag;?>

php_admin_value[open_basedir] = <?=$openbasedir;?>

php_admin_value[max_input_vars] = <?=$max_input_vars_flag;?>

php_admin_value[date.timezone] = "<?=$date_timezone_flag;?>"
