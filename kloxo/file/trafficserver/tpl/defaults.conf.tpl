<?php

foreach ($driverlist as $k => $v) {
	$srcinitpath = "/opt/configs/{$v}/etc/init.d";
	$trgtinitpath = "/etc/rc.d/init.d";

	if (file_exists("{$trgtinitpath}/{$v}")) {
		exec("service {$v} stop; chkconfig {$v} off");
		unlink("{$trgtinitpath}/{$v}");

		if ($v === 'varnish') {
			unlink("{$trgtinitpath}/{$v}log");
			unlink("{$trgtinitpath}/{$v}ncsa");
		}
	}
}

$srcinitpath = "/opt/configs/{$driver}/etc/init.d";
$trgtinitpath = "/etc/rc.d/init.d";

if ($driver === 'varnish') {
	$inits = array ('', 'log', 'ncsa');

	foreach ($inits as $k2 => $v2) {
		if (file_exists("{$srcinitpath}/custom.{$driver}{$v2}.init")) {
			copy("{$srcinitpath}/custom.{$driver}{$v2}.init", "{$trgtinitpath}/{$driver}{$v2}");
		} else {
			copy("{$srcinitpath}/{$driver}{$v2}.init", "{$trgtinitpath}/{$driver}{$v2}");
		}

		chmod("{$trgtinitpath}/{$driver}{$v2}", 755);
		exec("chkconfig {$driver}{$v2} on");
	}
} else {
	if (file_exists("{$srcinitpath}/custom.{$driver}.init")) {
		copy("{$srcinitpath}/custom.{$driver}.init", "{$trgtinitpath}/{$driver}");
	} else {
		copy("{$srcinitpath}/{$driver}.init", "{$trgtinitpath}/{$driver}");
	}

	chmod("{$trgtinitpath}/{$driver}", 755);
	exec("chkconfig {$driver} on");
}

$srcconfpath ="/opt/configs/trafficserver/etc/conf";
$trgtconfpath ="/etc/trafficserver";

$list = array('ip_allow', 'records', 'remap', 'storage');

foreach ($list as $k => $v) {
	if (file_exists("{$srcconfpath}/custom.{$v}.config")) {
		copy("{$srcconfpath}/custom.{$v}.config", "{$trgtconfpath}/{$v}.config");
	} else {
		copy("{$srcconfpath}/{$v}.config", "{$trgtconfpath}/{$v}.config");
	}
}

?>