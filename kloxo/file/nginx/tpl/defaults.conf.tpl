### begin - web of initial - do not remove/modify this line

<?php

$listens = array('listen_nonssl', 'listen_ssl');

foreach ($certnamelist as $ip => $certname) {
	$certnamelist[$ip] = "/home/kloxo/httpd/ssl/{$certname}";
}

$iplist = array('*');

$defaultdocroot = "/home/kloxo/httpd/default";
$cpdocroot = "/home/kloxo/httpd/cp";

$globalspath = "/opt/configs/nginx/conf/globals";

if ($indexorder) {
	$indexorder = implode(' ', $indexorder);
}

// MR -- for future purpose, apache user have uid 50000
// $userinfoapache = posix_getpwnam('apache');
// $fpmportapache = (50000 + $userinfoapache['uid']);
$fpmportapache = 50000;

exec("ip -6 addr show", $out);

if ($out[0]) {
	$IPv6Enable = true;
} else {
	$IPv6Enable = false;
}

if ($reverseproxy) {
	if (file_exists("{$globalspath}/custom.proxy_standard.conf")) {
		copy("{$globalspath}/custom.proxy_standard.conf", "{$globalspath}/switch_standard.conf");
	} else {
		copy("{$globalspath}/proxy_standard.conf", "{$globalspath}/switch_standard.conf");
	}

	if (file_exists("{$globalspath}/custom.proxy_wildcards.conf")) {
		copy("{$globalspath}/custom.proxy_wildcards.conf", "{$globalspath}/switch_wildcards.conf");
	} else {
		copy("{$globalspath}/proxy_wildcards.conf", "{$globalspath}/switch_wildcards.conf");
	}

	if (file_exists("{$globalspath}/custom.stats_none.conf")) {
		copy("{$globalspath}/custom.stats_none.conf", "{$globalspath}/stats.conf");
	} else {
		copy("{$globalspath}/stats_none.conf", "{$globalspath}/stats.conf");
	}
} else {
	if (file_exists("{$globalspath}/custom.php-fpm_standard.conf")) {
		copy("{$globalspath}/custom.php-fpm_standard.conf", "{$globalspath}/switch_standard.conf");
	} else {
		copy("{$globalspath}/php-fpm_standard.conf", "{$globalspath}/switch_standard.conf");
	}

	if (file_exists("{$globalspath}/custom.php-fpm_wildcards.conf")) {
		copy("{$globalspath}/custom.php-fpm_wildcards.conf", "{$globalspath}/switch_wildcards.conf");
	} else {
		copy("{$globalspath}/php-fpm_wildcards.conf", "{$globalspath}/switch_wildcards.conf");
	}

	if ($stats['app'] === 'webalizer') {
		if (file_exists("{$globalspath}/custom.stats_webalizer.conf")) {
			copy("{$globalspath}/custom.stats_webalizer.conf", "{$globalspath}/stats.conf");
		} else {
			copy("{$globalspath}/stats_webalizer.conf", "{$globalspath}/stats.conf");
		}

		if (file_exists("{$globalspath}/custom.dirprotect_webalizer.conf")) {
			copy("{$globalspath}/custom.dirprotect_webalizer.conf", "{$globalspath}/dirprotect_stats.conf");
		} else {
			copy("{$globalspath}/dirprotect_webalizer.conf", "{$globalspath}/dirprotect_stats.conf");
		}
	} else {
		if (file_exists("{$globalspath}/custom.stats_awstats.conf")) {
			copy("{$globalspath}/custom.stats_awstats.conf", "{$globalspath}/stats.conf");
		} else {
			copy("{$globalspath}/stats_awstats.conf", "{$globalspath}/stats.conf");
		}

		if (file_exists("{$globalspath}/custom.dirprotect_awstats.conf")) {
			copy("{$globalspath}/custom.dirprotect_awstats.conf", "{$globalspath}/dirprotect_stats.conf");
		} else {
			copy("{$globalspath}/dirprotect_awstats.conf", "{$globalspath}/dirprotect_stats.conf");
		}
	}
}

if (($webcache === 'none') || (!$webcache)) {
	if (file_exists("{$globalspath}/custom.listen_nonssl_front.conf")) {
		copy("{$globalspath}/custom.listen_nonssl_front.conf", "{$globalspath}/listen_nonssl.conf");
	} else {
		copy("{$globalspath}/listen_nonssl_front.conf", "{$globalspath}/listen_nonssl.conf");
	}

	if (file_exists("{$globalspath}/custom.listen_ssl_front.conf")) {
		copy("{$globalspath}/custom.listen_ssl_front.conf", "{$globalspath}/listen_ssl.conf");
	} else {
		copy("{$globalspath}/listen_ssl_front.conf", "{$globalspath}/listen_ssl.conf");
	}
} else {
	if (file_exists("{$globalspath}/custom.listen_nonssl_back.conf")) {
		copy("{$globalspath}/custom.listen_nonssl_back.conf", "{$globalspath}/listen_nonssl.conf");
	} else {
		copy("{$globalspath}/listen_nonssl_back.conf", "{$globalspath}/listen_nonssl.conf");
	}

	if (file_exists("{$globalspath}/custom.listen_ssl_back.conf")) {
		copy("{$globalspath}/custom.listen_ssl_back.conf", "{$globalspath}/listen_ssl.conf");
	} else {
		copy("{$globalspath}/listen_ssl_back.conf", "{$globalspath}/listen_ssl.conf");
	}
}

foreach ($certnamelist as $ip => $certname) {
	$count = 0;

	foreach ($listens as &$listen) {
?>

## 'default' config
server {
	#disable_symlinks if_not_owner;

	include '<?php echo $globalspath; ?>/<?php echo $listen; ?>.conf';
<?php
		if ($count !== 0) {
?>

	ssl on;
	ssl_certificate <?php echo $certname; ?>.pem;
	ssl_certificate_key <?php echo $certname; ?>.key;
	ssl_protocols SSLv3 TLSv1 TLSv1.1 TLSv1.2;
	ssl_ciphers HIGH:!aNULL:!MD5;
<?php
		}

?>

	server_name _;

	set $var_domain '';

	index <?php echo $indexorder; ?>;

	set $var_rootdir '<?php echo $defaultdocroot; ?>';

	root $var_rootdir;

	set $var_user 'apache';

	set $var_fpmport '<?php echo $fpmportapache; ?>';

	include '<?php echo $globalspath; ?>/switch_standard.conf';
<?php
		$count++;
?>
}

<?php
	}
}
?>

### end - web of initial - do not remove/modify this line
