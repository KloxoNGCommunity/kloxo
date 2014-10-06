<?php
	if (file_exists("/etc/rndc.conf")) {
		exec("'rm' -f /etc/rndc.conf");
	}

	if (!file_exists("/var/log/named")) {
		exec("mkdir -p /var/log/named; chmod -R 777 /var/log/named");

	}

	$d1names = $domains;

	$tpath = "/opt/configs/bind/conf/master";
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
		$zone = "zone \"{$v}\" {\n    type master;\n    file \"master/{$v}\";\n};\n\n";
		$str .= $zone;
	}

	$file = "/opt/configs/bind/conf/defaults/named.master.conf";

	file_put_contents($file, $str);

	if (!file_exists("/etc/rc.d/init.id/named")) { return; }

//	createRestartFile("restart-dns");
	exec_with_all_closed("rndc reload");
