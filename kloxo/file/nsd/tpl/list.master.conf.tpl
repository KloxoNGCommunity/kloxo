<?php
	$path = "/opt/configs/nsd/conf/master";
	$dirs = glob("{$path}/*");

	$str = '';

	foreach ($dirs as $k => $v) {
		$d = str_replace("{$path}/", "", $v);
		$zone  = "zone:\n    name: {$d}\n    zonefile: master/{$d}\n";

		$zone .= "    include: \"/opt/configs/nsd/conf/defaults/nsd.acl.conf\"\n";

		$str .= $zone . "\n";
	}

	$file = "/opt/configs/nsd/conf/defaults/nsd.master.conf";

	file_put_contents($file, $str);

	if (!file_exists("/etc/rc.d/init.id/nsd")) { return; }

//	createRestartFile("restart-dns");

	if (file_exists("/usr/sbin/nsd-control")) {
		$n = "/usr/sbin/nsd-control";
		exec_with_all_closed("{$n} write ; {$n} notify");
	} else {
		$n = "/usr/sbin/nsdc";
		exec_with_all_closed("{$n} rebuild ; {$n} notify");
	}


