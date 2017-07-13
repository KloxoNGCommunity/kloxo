### begin - web of initial - do not remove/modify this line


<?php

if (!file_exists("/var/log/lighttpd")) {
	mkdir("/var/log/lighttpd",0777);
}

chmod("/var/log/lighttpd", 0777);

if (!isset($phpselected)) {
	$phpselected = 'php';
}

if (!isset($timeout)) {
	$timeout = '300';
}

if (!file_exists("/var/run/letsencrypt/.well-known/acme-challenge")) {
	exec("mkdir -p /var/run/letsencrypt/.well-known/acme-challenge");
}

$srcconfpath = "/opt/configs/lighttpd/etc/conf";
$srcconfdpath = "/opt/configs/lighttpd/etc/conf.d";
$trgtconfpath = "/etc/lighttpd";
$trgtconfdpath = "/etc/lighttpd/conf.d";

$sslpath = "/home/kloxo/ssl";

$custom_conf = getLinkCustomfile($srcconfpath, "lighttpd.conf");
copy($custom_conf, "{$trgtconfpath}/lighttpd.conf");

$custom_conf = getLinkCustomfile($srcconfdpath, "~lxcenter.conf");
copy($custom_conf, "{$trgtconfdpath}/~lxcenter.conf");

if (($webcache === 'none') || (!$webcache)) {
	$ports[] = '80';
	$ports[] = '443';
} else {
	$ports[] = '8080';
	$ports[] = '8443';
}

$portlist = array('var.port', 'var.portssl');

$globalspath = "/opt/configs/lighttpd/conf/globals";

if ($stats['app'] === 'webalizer') {
	$confs = array('php-fpm_standard' => 'switch_standard', 'stats_webalizer' => 'stats');
} else {
	$confs = array('php-fpm_standard' => 'switch_standard', 'stats_awstats' => 'stats');
}

foreach ($confs as $k => $v) {
	$custom_conf = getLinkCustomfile($globalspath, "{$k}.conf");
	copy($custom_conf, "{$globalspath}/{$v}.conf");
}

$acmechallenge_conf = getLinkCustomfile($globalspath, "acme-challenge.conf");

foreach ($certnamelist as $ip => $certname) {
	$cert_ip = $ip;
	$cert_file = "{$sslpath}/{$certname}";
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

$tabs = array("", "\t");
?>
## MR -- ref: https://www.kb.cert.org/vuls/id/JLAD-ABZJ3A
#server.modules += ( "mod_magnet" )
magnet.attract-raw-url-to = ( "/opt/configs/lighttpd/conf/globals/deny-proxy.lua" )

evasive.max-conns-per-ip = 50
server.errorfile-prefix = "/home/kloxo/httpd/error/"

server.port = "<?=$ports[0];?>"


$SERVER["socket"] == ":<?=$ports[1];?>" {

	ssl.engine = "enable"

	ssl.pemfile = "<?=$cert_file;?>.pem"
	ssl.dh-file = "/etc/ssl/certs/dhparam.pem"
<?php
if (file_exists("{$cert_file}.ca")) {
?>
	ssl.ca-file = "<?=$cert_file;?>.ca"
<?php
}

$dirs = glob("{$sslpath}/*.pem", GLOB_MARK);

if (count($dirs) > 0) {
	foreach($dirs as $k => $v) {
		$f = str_replace(".pem", "", $v);
		$d = str_replace("{$sslpath}/", "", $f);

		if ($certname === $d) { continue; }
?>

	$HTTP["host"] =~ "(^|www\.|cp\.|webmail\.|mail\.)<?=str_replace(".", "\.", $d);?>" {

		ssl.pemfile = "<?=$v;?>"
<?php
		if (file_exists("{$f}.ca")) {
?>
		ssl.ca-file = "<?=$f;?>.ca"
<?php
		}
?>

		ssl.use-sslv2 = "disable"
		ssl.use-sslv3 = "disable"
		sl.use-compression = "disable"
		ssl.honor-cipher-order = "enable"
		ssl.cipher-list = "ECDH+AESGCM:DH+AESGCM:ECDH+AES256:DH+AES256:ECDH+AES128:DH+AES:ECDH+3DES:DH+3DES:RSA+AESGCM:RSA+AES:RSA+3DES:!aNULL:!MD5:!DSS"

	}
<?php
	}
}
?>

}


$HTTP["host"] =~ "^default\.*" {

	server.follow-symlink = "enable"

	include "<?=$acmechallenge_conf;?>"

	var.rootdir = "/home/kloxo/httpd/default/"
	var.user = "apache"
	var.fpmport = "<?=$fpmportapache;?>"
	var.phpselected = "php"
	var.timeout = "<?=$timeout;?>"

	server.document-root = var.rootdir

	index-file.names = ( <?=$indexorder;?> )

	include "<?=$globalspath;?>/switch_standard.conf"

}


### end - web of initial - do not remove/modify this line
