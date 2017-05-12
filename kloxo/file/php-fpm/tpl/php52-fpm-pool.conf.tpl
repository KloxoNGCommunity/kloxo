<?php
	if (!file_exists("/opt/configs/php-fpm/sock")) {
		mkdir("/opt/configs/php-fpm/sock");
	}

/*
	$startservers = (($sts = (int)($maxchildren / 3 * 2)) < 2) ? 2 : $sts;
	$minspareservers = (($mis = (int)($maxchildren / 3)) < 2) ? 2 : $mis;
	$maxspareservers = (($mas = (int)($maxchildren / 3 * 2)) < 2) ? 2 : $mas;
	$maxchildren = (($mac = (int)($maxchildren)) < 2) ? 2 : $mac;
*/
	if (!$maxchildren) {
	//	$maxchildren = '6';
		$maxchildren = '2';
	} else {
		$maxchildren = (int)$maxchildren / 3;

		if ((int)$maxchildren < 1) {
			$maxchildren = '1';
		}
	}

	$userlist[] = 'apache';

	if ($user === 'apache') {
		// MR -- for future purpose, apache user have uid 50000
		$fpmport = 50000;
		$openbasedir = "/home/:/tmp/:/usr/share/pear/:/var/lib/php/session/";
		$pool = 'default';
	} else {
		$userinfo = posix_getpwnam($user);
		$fpmport = (50000 + $userinfo['uid']);
		$pool = $user;
		$openbasedir = "/home/$user/:/tmp/:/usr/share/pear/:/var/lib/php/session/:".
			"/home/kloxo/httpd/script/:/home/kloxo/httpd/disable/:{$extrabasedir}";
	}
?>
		<section name="pool">
			<value name="name">php52m-<?=$pool;?></value>
			<!-- <value name="listen_address">127.0.0.1:<?=$fpmport;?></value> -->
			<value name="listen_address">/opt/configs/php-fpm/sock/php52m-<?=$user;?>.sock</value>
			<value name="listen_options">
				<value name="backlog">65536</value>
				<value name="owner"><?=$user;?></value>
				<value name="group"><?=$user;?></value>
				<value name="mode">0666</value>
			</value>
			<value name="user"><?=$user;?></value>
			<value name="group"><?=$user;?></value>
			<value name="pm">
				<value name="style">static</value>
				<!-- <value name="style">apache_like</value> -->
				<value name="max_children"><?=$maxchildren;?></value>
				<value name="apache_like">
					<value name="StartServers"><?=$startservers;?></value>
					<value name="MinSpareServers"><?=$minspareservers;?></value>
					<value name="MaxSpareServers"><?=$maxspareservers;?></value>
				</value>
			</value>
			<value name="request_terminate_timeout"><?=$max_execution_time_flag;?>s</value>
			<value name="request_slowlog_timeout">30s</value>
			<value name="slowlog">/var/log/php-fpm/slow.log</value>
			<value name="rlimit_files">10240</value>
			<value name="rlimit_core">0</value>
			<value name="chroot"></value>
			<value name="chdir"></value>
			<value name="catch_workers_output">yes</value>
			<value name="max_requests">1000</value>
			<value name="allowed_clients">127.0.0.1</value>
			<value name="environment">
				<value name="HOSTNAME">$HOSTNAME</value>
				<value name="PATH">/bin:/usr/bin:/sbin:/usr/sbin:/usr/local/bin</value>
				<value name="TMP">/tmp</value>
				<value name="TMPDIR">/tmp</value>
				<value name="TEMP">/tmp</value>
				<value name="OSTYPE">$OSTYPE</value>
				<value name="MACHTYPE">$MACHTYPE</value>
				<value name="MALLOC_CHECK_">2</value>
			</value>
			<value name="php_defines">
				<value name="zlib.output_compression"><?=$output_compression_flag;?></value>
				<value name="disable_functions"><?=$disable_functions;?></value>
				<value name="display_errors"><?=$display_error_flag;?></value>
				<value name="file_uploads"><?=$file_uploads_flag;?></value>
				<value name="upload_max_filesize"><?=$upload_max_filesize;?></value>
				<value name="log_errors"><?=$log_errors_flag;?></value>
				<value name="output_buffering"><?=$output_buffering_flag;?></value>
				<value name="register_argc_argv"><?=$register_argc_argv_flag;?></value>
				<value name="max_execution_time"><?=$max_execution_time_flag;?></value>
				<value name="max_input_time"><?=$max_input_time_flag;?></value>
				<value name="memory_limit"><?=$memory_limit_flag;?></value>
				<value name="post_max_size"><?=$post_max_size_flag;?></value>
				<value name="allow_url_fopen"><?=$allow_url_fopen_flag;?></value>
				<value name="allow_url_include"><?=$allow_url_include_flag;?></value>
				<value name="session.save_path"><?=$session_save_path_flag;?></value>
				<value name="cgi.force_redirect"><?=$cgi_force_redirect_flag;?></value>
				<value name="enable_dl"><?=$enable_dl_flag;?></value>
				<value name="open_basedir"><?=$openbasedir;?></value>
				<value name="max_input_vars"><?=$max_input_vars_flag;?></value>
				<value name="date.timezone"><?=$date_timezone_flag;?></value>
			</value>
		</section>

