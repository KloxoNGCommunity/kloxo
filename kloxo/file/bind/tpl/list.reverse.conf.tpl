<?php
	exec("cat '' > /opt/configs/bind/conf/defaults/bind.reverse.conf");

	$d1names = $arpas;

	// MR -- use nsd data because the same structure
	$tpath = "/opt/configs/nsd/conf/reverse";
	$d2files = glob("{$tpath}/*");

	if (empty($d2files)) { return; }

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
		$zone = "zone \"{$v}\" in {\n    type master;\n    file \"reverse/{$v}\";\n};\n\n";
		$str .= $zone;
	}

	$file = "/opt/configs/bind/conf/defaults/named.reverse.conf";

	file_put_contents($file, $str);

	if (!file_exists("/etc/rc.d/init.d/named")) { return; }

	createRestartFile("restart-dns");
