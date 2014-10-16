### begin - web of initial - do not remove/modify this line

<?php

$srcconfpath = "/opt/configs/nginx/etc/conf";
$srcconfdpath = "/opt/configs/nginx/etc/conf.d";
$trgtconfpath = "/etc/nginx";
$trgtconfdpath = "/etc/nginx/conf.d";

$confs = array('nginx.conf', 'mime.types', 'fastcgi_params');

foreach ($confs as $k => $v) {
	if (file_exists("{$srcconfpath}/custom.{$v}")) {
		copy("{$srcconfpath}/custom.{$v}", "{$trgtconfpath}/{$v}");
	} else {
		copy("{$srcconfpath}/{$v}", "{$trgtconfpath}/{$v}");
	}
}

$confs = array('~lxcenter.conf', 'default.conf');

foreach ($confs as $k => $v) {
	if (file_exists("{$srcconfdpath}/custom.{$v}")) {
		copy("{$srcconfdpath}/custom.{$v}", "{$trgtconfdpath}/{$v}");
	} else {
		copy("{$srcconfdpath}/{$v}", "{$trgtconfdpath}/{$v}");
	}
}

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
	$confs = array('proxy_standard' => 'switch_standard', 'proxy_wildcards' => 'switch_wildcards',
		'stats_none' => 'stats', 'dirprotect_none' => 'dirprotect_stats');
} else {
	if ($stats['app'] === 'webalizer') {
		$confs = array('php-fpm_standard' => 'switch_standard', 'php-fpm_wildcards' => 'switch_wildcards',
		'stats_webalizer' => 'stats', 'dirprotect_webalizer' => 'dirprotect_stats');
	} else {
		$confs = array('php-fpm_standard' => 'switch_standard', 'php-fpm_wildcards' => 'switch_wildcards',
		'stats_awstats' => 'stats', 'dirprotect_awstats' => 'dirprotect_stats');
	}

}

foreach ($confs as $k => $v) {
	if (file_exists("{$globalspath}/custom.{$k}.conf")) {
		copy("{$globalspath}/custom.{$k}.conf", "{$globalspath}/{$v}.conf");
	} else {
		copy("{$globalspath}/{$k}.conf", "{$globalspath}/{$v}.conf");
	}
}

if (($webcache === 'none') || (!$webcache)) {
	$confs = array('listen_nonssl_front' => 'listen_nonssl', 'listen_ssl_front' => 'listen_ssl',
		'listen_nonssl_front_default' => 'listen_nonssl_default', 'listen_ssl_front_default' => 'listen_ssl_default');
} else {
	$confs = array('listen_nonssl_back' => 'listen_nonssl', 'listen_ssl_back' => 'listen_ssl',
		'listen_nonssl_back_default' => 'listen_nonssl_default', 'listen_ssl_back_default' => 'listen_ssl_default');
}

foreach ($confs as $k => $v) {
	if (file_exists("{$globalspath}/custom.{$k}.conf")) {
		copy("{$globalspath}/custom.{$k}.conf", "{$globalspath}/{$v}.conf");
	} else {
		copy("{$globalspath}/{$k}.conf", "{$globalspath}/{$v}.conf");
	}
}

$listens = array('listen_nonssl_default', 'listen_ssl_default');

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
	ssl_protocols TLSv1 TLSv1.1 TLSv1.2;
	#ssl_ciphers HIGH:!aNULL:!MD5;
	ssl_ciphers ECDH+AESGCM:DH+AESGCM:ECDH+AES256:DH+AES256:ECDH+AES128:DH+AES:ECDH+3DES:DH+3DES:RSA+AESGCM:RSA+AES:RSA+3DES:!aNULL:!MD5:!DSS;
	#ssl_prefer_server_ciphers on;
	ssl_session_cache builtin:1000 shared:SSL:10m;
<?php
		}

?>

	server_name _;

	index <?php echo $indexorder; ?>;

	set $var_domain '';

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
