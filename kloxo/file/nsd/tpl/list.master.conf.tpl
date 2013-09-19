<?php


	$path = "/home/nsd/conf/master";
	$dirs = glob("{$path}/*");

	$str = '';

	foreach ($dirs as $d) {
		$d = str_replace("{$path}/", "", $d);
		$zone  = "zone:\n    name: {$d}\n    zonefile: master/{$d}\n";

	/*
		if (array_keys($ip)) {
			foreach ($ip as $k => $v) {
				$zone .= "    notify: {$v} NOKEY\n    provide-xfr: {$v} NOKEY\n";
			}
		}
	*/

		$zone .= "    notify: 0.0.0.0 NOKEY\n    provide-xfr: 0.0.0.0 NOKEY\n";
		
		$str .= $zone . "\n";
	}

	$file = "/home/nsd/conf/defaults/nsd.master.conf";

	file_put_contents($file, $str);

//	exec("nsdc rebuild; nsdc notify");
	exec("nsdc rebuild");

?>

