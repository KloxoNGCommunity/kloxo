### begin - web of initial - do not remove/modify this line

<?php

$srcpath = "/opt/configs/nginx";

$custom_conf = getLinkCustomfile("{$srcpath}/etc/sysconfig", "spawn-fcgi");
copy($custom_conf, "/etc/sysconfig/spawn-fcgi");

if (!isset($phpselected)) {
	$phpselected = 'php';
}

if (!isset($timeout)) {
	$timeout = '300';
}

@exec("chown -R apache:apache /var/cache/nginx*");

$vsnpath = "/var/cache/nginx";

if (!file_exists("{$vsnpath}/proxy_temp")) {
	exec("mkdir -p {$vsnpath}/proxy_temp");
	@exec("chown -R apache:apache {$vsnpath}/proxy_temp");
}

if (!file_exists("{$vsnpath}/fastcgi_temp")) {
	exec("mkdir -p {$vsnpath}/fastcgi_temp");
	@exec("chown -R apache:apache {$vsnpath}/fastcgi_temp");
}

$vrlw = "/var/run/letsencrypt/.well-known/acme-challenge";

if (!file_exists($vrlw)) {
	exec("mkdir -p {$vrlw}");
}

$srcconfpath = "{$srcpath}/etc/conf";
$srcconfdpath = "{$srcpath}/etc/conf.d";
$trgtconfpath = "/etc/nginx";
$trgtconfdpath = "{$trgtconfpath}/conf.d";

$defaultdocroot = "/home/kloxo/httpd/default";

$globalspath = "{$srcpath}/conf/globals";

$confs = array('nginx.conf', 'mime.types', 'fastcgi_params');

$switches = array('', '_ssl');

foreach ($confs as $k => $v) {
	$custom_conf = getLinkCustomfile($srcconfpath, $v);
	copy($custom_conf, "{$trgtconfpath}/{$v}");
}

$confs = array('~lxcenter.conf', 'default.conf');

foreach ($confs as $k => $v) {
	$custom_conf = getLinkCustomfile($srcconfdpath, $v);
	copy($custom_conf, "{$trgtconfdpath}/{$v}");
}

foreach ($certnamelist as $ip => $certname) {
	$certnamelist[$ip] = "/home/kloxo/ssl/{$certname}";
}

$iplist = array('*');

if ($indexorder) {
	$indexorder = implode(' ', $indexorder);
}

// MR -- for future purpose, apache user have uid 50000
// $userinfoapache = posix_getpwnam('apache');
// $fpmportapache = (50000 + $userinfoapache['uid']);
$fpmportapache = 50000;

$out = null;
exec("ip -6 addr show", $out);

if (count($out) > 0) {
	$IPv6Enable = true;
} else {
	$IPv6Enable = false;
}

if ($reverseproxy) {
	$confs = array('proxy_standard' => 'switch_standard', 'proxy_wildcards' => 'switch_wildcards',
		'proxy_standard_ssl' => 'switch_standard_ssl', 'proxy_wildcards_ssl' => 'switch_wildcards_ssl');
} else {
	$confs = array('php-fpm_standard' => 'switch_standard', 'php-fpm_wildcards' => 'switch_wildcards',
		'php-fpm_standard_ssl' => 'switch_standard_ssl', 'php-fpm_wildcards_ssl' => 'switch_wildcards_ssl');
}

if ($stats['app'] === 'webalizer') {
	$confs = array_merge($confs, array('stats_webalizer' => 'stats'));
} else {
	$confs = array_merge($confs, array('stats_awstats' => 'stats'));
}

if (($webcache === 'none') || (!$webcache)) {
	$out = null;
	exec("echo $(2>&1 nginx -V | tr -- - '\n' | grep _module)|tr ' ' '\n'|grep 'http_v2_module'", $out);

	if (count($out) > 0) {
		$confs = array_merge($confs, array('listen_nonssl_front' => 'listen_nonssl', 'listen_ssl_front_h2' => 'listen_ssl',
			'listen_nonssl_front_default' => 'listen_nonssl_default', 'listen_ssl_front_default_h2' => 'listen_ssl_default'));
	} else {
		$confs = array_merge($confs, array('listen_nonssl_front' => 'listen_nonssl', 'listen_ssl_front' => 'listen_ssl',
			'listen_nonssl_front_default' => 'listen_nonssl_default', 'listen_ssl_front_default' => 'listen_ssl_default'));
	}
} else {
	$confs = array_merge($confs, array('listen_nonssl_back' => 'listen_nonssl', 'listen_ssl_back' => 'listen_ssl',
		'listen_nonssl_back_default' => 'listen_nonssl_default', 'listen_ssl_back_default' => 'listen_ssl_default'));
}

foreach ($confs as $k => $v) {
	$custom_conf = getLinkCustomfile($globalspath, "{$k}.conf");
	copy($custom_conf, "{$globalspath}/{$v}.conf");
}

$gzip_base_conf = getLinkCustomfile($globalspath, "gzip.conf");

$ssl_base_conf = getLinkCustomfile($globalspath, "ssl_base.conf");

$acmechallenge_conf = getLinkCustomfile($globalspath, "acme-challenge.conf");

$listens = array('listen_nonssl_default', 'listen_ssl_default');

foreach ($certnamelist as $ip => $certname) {
	$count = 0;

	foreach ($listens as &$listen) {
?>

## 'default' config
server {
	#disable_symlinks if_not_owner;

	include '<?=getLinkCustomfile($globalspath, "{$listen}.conf");?>';

	include '<?=$gzip_base_conf;?>';
<?php
		if ($count !== 0) {
?>

	include '<?=$ssl_base_conf;?>';
	ssl_certificate <?=$certname;?>.pem;
	ssl_certificate_key <?=$certname;?>.key;
<?php
			if (file_exists("{$certname}.ca")) {
?>
	ssl_trusted_certificate <?=$certname;?>.ca;
<?php
			}
		}

?>

	server_name _;

	include '<?=$acmechallenge_conf;?>';

	index <?=$indexorder;?>;

	set $var_domain '';
	set $var_rootdir '<?=$defaultdocroot;?>';

	root $var_rootdir;

	set $var_user 'apache';
	set $var_fpmport '<?=$fpmportapache;?>';
	set $var_phpselected 'php';
<?php
		//if ((!$reverseproxy) || (($reverseproxy) && ($webselected === 'front-end'))) {
?>

	proxy_connect_timeout <?=$timeout;?>s;
	proxy_send_timeout <?=$timeout;?>s;
	proxy_read_timeout <?=$timeout;?>s;
<?php
		//} else {
?>

	fastcgi_connect_timeout <?=$timeout;?>s;
	fastcgi_send_timeout <?=$timeout;?>s;
	fastcgi_read_timeout <?=$timeout;?>s;
<?php
		//}
?>

	include '<?=getLinkCustomfile($globalspath, "switch_standard{$switches[$count]}.conf");?>';
<?php
		$count++;
?>
}

<?php
	}
}
?>

### end - web of initial - do not remove/modify this line
