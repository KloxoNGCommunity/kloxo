<?php
	$path = "/home/maradns/conf/master";
	$dirs = glob("{$path}/*");

	$str = '';

	foreach ($dirs as $d) {
		$d = str_replace("{$path}/", "", $d);
		$zone = "csv1[\"{$d}\"] = \"master/{$d}\"\n";
		$str .= $zone;
	}

	$file = "/home/maradns/conf/defaults/maradns.master.conf";

	file_put_contents($file, $str);
?>

csv1[\"{$d}\"] = \"master/{$d}\"