### begin - web of initial - do not remove/modify this line


<?php

$srcconfpath = "/opt/configs/lighttpd/etc/conf";
$srcconfdpath = "/opt/configs/lighttpd/etc/conf.d";
$trgtconfpath = "/etc/lighttpd";
$trgtconfdpath = "/etc/lighttpd/conf.d";

if (file_exists("{$srcconfpath}/custom.lighttpd.conf")) {
	copy("{$srcconfpath}/custom.lighttpd.conf", "{$trgtconfpath}/lighttpd.conf");
} else {
	copy("{$srcconfpath}/lighttpd.conf", "{$trgtconfpath}/lighttpd.conf");
}

if (file_exists("{$srcconfdpath}/custom.~lxcenter.conf")) {
	copy("{$srcconfdpath}/custom.~lxcenter.conf", "{$trgtconfdpath}/~lxcenter.conf");
} else {
	copy("{$srcconfdpath}/~lxcenter.conf", "{$trgtconfdpath}/~lxcenter.conf");
}

if (($webcache === 'none') || (!$webcache)) {
	$ports[] = '80';
	$ports[] = '443';
} else {
	$ports[] = '8080';
	$ports[] = '8443';
}

$portlist = array('var.port', 'var.portssl');

$globalspath = "/opt/configs/lighttpd/conf/globals";

if ($reverseproxy) {
	if (file_exists("{$globalspath}/custom.proxy_standard.conf")) {
		copy("{$globalspath}/custom.proxy_standard.conf", "{$globalspath}/switch_standard.conf");
	} else {
		copy("{$globalspath}/proxy_standard.conf", "{$globalspath}/switch_standard.conf");
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

foreach ($certnamelist as $ip => $certname) {
	$certnamelist[$ip] = "/home/kloxo/httpd/ssl/{$certname}";
}

if ($indexorder) {
	$indexorder = implode(' ', $indexorder);
}

$indexorder = '"' . $indexorder . '"';
$indexorder = str_replace(' ', '", "', $indexorder);

// MR -- for future purpose, apache user have uid 50000
// $userinfoapache = posix_getpwnam('apache');
// $fpmportapache = (50000 + $userinfoapache['uid']);
$fpmportapache = 50000;

$count = 0;
?>
server.port = "<?php echo $ports[0]; ?>"
<?php echo $portlist[1]; ?> = "<?php echo $ports[1]; ?>"

<?php

foreach ($ports as &$port) {
	if ($count !== 0) {
		foreach ($certnamelist as $ip => $certname) {
?>

$SERVER["socket"] == ":" + <?php echo $portlist[$count]; ?> {

	ssl.engine = "enable"

	ssl.pemfile = "<?php echo $certname; ?>.pem"
<?php
			if (file_exists("{$certname}.ca")) {

?>

	ssl.ca-file = "<?php echo $certname; ?>.ca"
<?php
			}
?>
	ssl.use-sslv2 = "disable"

<?php
		}
	}
?>

	$HTTP["host"] =~ "^default\.*" {

		var.rootdir = "/home/kloxo/httpd/default/"
		var.user = "apache"
		var.fpmport = "<?php echo $fpmportapache; ?>"

		server.document-root = var.rootdir

		index-file.names = ( <?php echo $indexorder; ?> )

		include "<?php echo $globalspath; ?>/switch_standard.conf"

	}

<?php
	if ($count !== 0) {
?>
}
<?php
	}

	$count++;
}
?>


### end - web of initial - do not remove/modify this line
