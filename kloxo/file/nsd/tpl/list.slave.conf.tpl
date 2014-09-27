<?php
	$path = "/opt/configs/dnsslave_tmp";
	$dirs = glob("{$path}/*");

	exec("'rm' -rf /opt/configs/nsd/conf/slave/*");

	$srr = '';

	foreach ($dirs as $d) {
		$c = trim(file_get_contents($d));
		$d = str_replace("{$path}/", "", $d);
		$zone  = "zone:\n    name: {$d}\n    zonefile: slave/{$d}\n";
		$zone .= "    request-xfr: {$c}@53 NOKEY\n";

		$str .= $zone . "\n";
	}

	$file = "/opt/configs/nsd/conf/defaults/nsd.slave.conf";

	file_put_contents($file, $str);

	createRestartFile("restart-dns");