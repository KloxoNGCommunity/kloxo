<?php
	exec("cat '' > /opt/configs/yadifa/conf/defaults/yadifa.slave.conf");

	foreach($domains as $k => $v) {
		$t = explode(':', $v);

		$d1names[] = $t[0];
		$d1ips[] = $t[1];
	}

	// MR -- use nsd data because the same structure
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
		
		$zone  = "<zone>";
		$zone .= "\n    domain      {$v}";
		$zone .= "\n    type        slave";
		$zone .= "\n    master      {$c}";
		$zone .= "\n    file-name   slave/{$v}";
		$zone .= "\n</zone>\n\n";

		$str .= $zone;
	}

	$file = "/opt/configs/yadifa/conf/defaults/yadifa.slave.conf";

	file_put_contents($file, $str);

	if (!file_exists("/etc/rc.d/init.d/yadifad")) { return; }

	createRestartFile("restart-dns");

