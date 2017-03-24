<?php
	exec("echo '' > /opt/configs/bind/conf/defaults/named.master.conf");

	$d1names = $domains;

	// MR -- use nsd data because the same structure
	$tpath = "/opt/configs/nsd/conf/master";
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
		$zone  = "zone \"{$v}\" in {\n";
		$zone .= "    type master;\n";
		$zone .= "    file \"master/{$v}\";\n";
		$zone .= "};\n";

		$str .= $zone . "\n";
	}

	$file = "/opt/configs/bind/conf/defaults/named.master.conf";

	file_put_contents($file, $str);
