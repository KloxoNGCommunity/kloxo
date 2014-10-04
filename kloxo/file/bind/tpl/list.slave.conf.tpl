<?php
	$path = "/opt/configs/dnsslave_tmp";

	if (!file_exists($path)) { return; }

	$dirs = glob("{$path}/*");

	exec("chown -R 777 /opt/configs/bind/conf/slave");

	exec("'rm' -rf /opt/configs/bind/conf/slave/*");

	$str = '';

	$doms = array();

	foreach ($dirs as $d) {
		$c = trim(file_get_contents($d));
		$d = str_replace("{$path}/", "", $d);
		
		$doms[] = $d;

		$zone  = "zone \"{$d}\" {\n    type slave;";
		$zone .= "\n    file \"slave/{$d}\";";
		$zone .= "\n    masters { {$c}; };";
		$zone .= "\n    masterfile-format text;";
		$zone .= "\n};\n\n";

		$str .= $zone;
	}

	$file = "/opt/configs/bind/conf/defaults/named.slave.conf";

	file_put_contents($file, $str);
/*
	foreach ($doms as $k => $v) {
		exec_with_all_closed("rndc retransfer {$v}");
	}
*/
	createRestartFile("restart-dns");
