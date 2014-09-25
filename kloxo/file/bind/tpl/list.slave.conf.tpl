<?php
	$path = "/opt/configs/bind/conf/slave";
	$dirs = glob("{$path}/*");

	$str = '';

	foreach ($dirs as $d) {
		$d = str_replace("{$path}/", "", $d);
		$zone = "zone \"{$d}\" { type slave; file \"slave/{$d}\"; allow-notify \"allow-notify\";};\n";
		$str .= $zone;
	}

	$file = "/opt/configs/bind/conf/defaults/named.slave.conf";

	file_put_contents($file, $str);
?>