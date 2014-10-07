<?php
	foreach($domains as $k => $v) {
		$t = explode(':', $v);

		$d1names[] = $t[0];
		$d1ips[] = $t[1];
	}

	$tpath = "/opt/configs/nsd/conf/slave";

	$d2files = glob("{$tpath}/*");

	foreach ($d2files as $k => $v) {
		$d2names[] = str_replace("{$tpath}/", '', $v);
	}

	$d2olds = array_diff($d2names, $d1names);

	// MR -- delete unwanted files
	if (!empty($d2olds)) {
		foreach ($d2olds as $k => $v) {
			unlink("{$tpath}/{$v}");
		}
	}

	$str = '';

	foreach ($d1names as $k => $v) {
		$c = $d1ips[$k];

		$zone  = "zone:\n    name: {$v}\n    zonefile: slave/{$v}\n";
		$zone .= "    request-xfr: {$c}@53 NOKEY\n";

		$str .= $zone . "\n";
	}

	$file = "/opt/configs/nsd/conf/defaults/nsd.slave.conf";

	file_put_contents($file, $str);

	if (!file_exists("/etc/rc.d/init.d/nsd")) { return; }

	if (file_exists("/usr/sbin/nsd-control")) {
		$n = "/usr/sbin/nsd-control";
		exec_with_all_closed("{$n} transfer; {$n} write; {$n} reload");
	} else {
		$n = "/usr/sbin/nsdc";
		exec_with_all_closed("{$n} update; {$n} rebuild; {$n} reload");
	}

//	createRestartFile("restart-dns");