<?php
	$path = "/opt/configs/dnsslave_tmp";
	$dirs = glob("{$path}/*");

	$str = '';

	foreach ($dirs as $d) {
		$c = trim(file_get_contents($d));
		$d = str_replace("{$path}/", "", $d);
		$zone  = "zone:\n    name: {$d}\n    zonefile: slave/{$d}\n";
		$zone .= "    request-xfr: {$c}@53 NOKEY\n";

		$str .= $zone . "\n";
	}

	$file = "/opt/configs/nsd/conf/defaults/nsd.slave.conf";

	file_put_contents($file, $str);

	if (file_exists("/usr/sbin/nsd-control")) {
		$nsdc = "/usr/sbin/nsd-control";
	} else {
		$nsdc = "/usr/sbin/nsdc";
	}

	exec_with_all_closed("{$nsdc} update; {$nsdc} reload");

//	exec_with_all_closed("/etc/init.d/nsd restart");

?>