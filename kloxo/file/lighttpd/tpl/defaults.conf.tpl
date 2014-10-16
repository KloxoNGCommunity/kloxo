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
	$confs = array('proxy_standard' => 'switch_standard', 'stats_none' => 'stats', 
		'dirprotect_none' => 'dirprotect_stats');
} else {
	if ($stats['app'] === 'webalizer') {
		$confs = array('php-fpm_standard' => 'switch_standard', 'stats_webalizer' => 'stats',
			'dirprotect_webalizer' => 'dirprotect_stats');
	} else {
		$confs = array('php-fpm_standard' => 'switch_standard', 'stats_awstats' => 'stats',
			'dirprotect_awstats' => 'dirprotect_stats');
	}

}

foreach ($confs as $k => $v) {
	if (file_exists("{$globalspath}/custom.{$k}.conf")) {
		copy("{$globalspath}/custom.{$k}.conf", "{$globalspath}/{$v}.conf");
	} else {
		copy("{$globalspath}/{$k}.conf", "{$globalspath}/{$v}.conf");
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

$tabs = array("", "\t");
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
	ssl.use-sslv3 = "disable"
<?php
		}
	}
?>

<?php echo $tabs[$count]; ?>$HTTP["host"] =~ "^default\.*" {

<?php echo $tabs[$count]; ?>	var.rootdir = "/home/kloxo/httpd/default/"
<?php echo $tabs[$count]; ?>	var.user = "apache"
<?php echo $tabs[$count]; ?>	var.fpmport = "<?php echo $fpmportapache; ?>"

<?php echo $tabs[$count]; ?>	server.document-root = var.rootdir

<?php echo $tabs[$count]; ?>	index-file.names = ( <?php echo $indexorder; ?> )

<?php echo $tabs[$count]; ?>	include "<?php echo $globalspath; ?>/switch_standard.conf"

<?php echo $tabs[$count]; ?>}

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
