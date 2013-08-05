<?php
	$path = "/home/bind/conf/master";
	$dirs = glob("{$path}/*");

	$str = '';

	foreach ($dirs as $d) {
		$d = str_replace("{$path}/", "", $d);
		$zone = "zone \"{$d}\" { type master; file \"master/{$d}\"; };\n";
		$str .= $zone;
	}

	$file = "/home/bind/conf/defaults/named.master.conf";

	file_put_contents($file, $str);
?>