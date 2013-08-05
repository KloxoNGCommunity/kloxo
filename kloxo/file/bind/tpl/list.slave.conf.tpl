<?php
	$path = "/home/bind/conf/slave";
	$dirs = glob("{$path}/*");

	$str = '';

	foreach ($dirs as $d) {
		$d = str_replace("{$path}/", "", $d);
		$zone = "zone \"{$d}\" { type slave; file \"slave/{$d}\"; };\n";
		$str .= $zone;
	}

	$file = "/home/bind/conf/defaults/named.slave.conf";

	file_put_contents($file, $str);
?>