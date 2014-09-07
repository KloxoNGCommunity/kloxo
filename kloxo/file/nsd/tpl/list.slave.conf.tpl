<?php
	$path = "/opt/configs/nsd/conf/slave";
	$dirs = glob("{$path}/*");

	$str = '';

	foreach ($dirs as $d) {
		$d = str_replace("{$path}/", "", $d);
		$zone = "zone:\n    name: {$d}\n    zonefile: slave/{$d}\n\n";
	//	$zone .= "    include: \"/opt/configs/nsd/conf/defaults/nsd.acl.conf\"\n";

		if (array_keys($ips)) {
			foreach ($ips as $k => $v) {
				$zone .= "    allow-notify: {$v} NOKEY\n    request-xfr: {$v}@53 NOKEY\n";
			}
		}

		$str .= $zone;
	}

	$file = "/opt/configs/nsd/conf/defaults/nsd.slave.conf";

	file_put_contents($file, $str);
?>