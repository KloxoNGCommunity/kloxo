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

foreach ($driver as $k => $v) {
	$srcinitpath = "/opt/configs/{$v}/etc/init.d";
	$trgtinitpath = "/etc/rc.d/init.d";

	if ($v === 'varnish') {
		$inits = array ('', 'log', 'ncsa');

		foreach ($inits as $k2 => $v2) {
			if (file_exists("{$srcinitpath}/custom.{$v}{$v2}.init")) {
				copy("{$srcinitpath}/custom.{$v}{$v2}.init", "{$trgtinitpath}/{$v}{$v2}");
			} else {
				copy("{$srcinitpath}/{$v}{$v2}.init", "{$trgtinitpath}/{$v}{$v2}");
			}
		}
	} else {
		if (file_exists("{$srcinitpath}/custom.{$v}.init")) {
			copy("{$srcinitpath}/custom.{$v}.init", "{$trgtinitpath}/{$v}");
		} else {
			copy("{$srcinitpath}/{$v}.init", "{$trgtinitpath}/{$v}");
		}
	}

	chmod("{$trgtinitpath}/{$v}", 755);
	exec("chkconfig {$v} on");
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