<?php
	$path = "/opt/configs/dnsslave_tmp";
	$dirs = glob("{$path}/*");

	exec("'rm' -rf /opt/configs/bind/conf/slave/*");

	$str = '';

	$doms = array();

	foreach ($dirs as $d) {
		$c = trim(file_get_contents($d));
		$d = str_replace("{$path}/", "", $d);
		
		$doms[] = $d;

		$zone = "zone \"{$d}\" {\n    type slave;\n    file \"slave/{$d}\";\n    masters { {$c}; };\n};\n\n";
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
