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

/*
	if (file_exists("/usr/sbin/nsd-control")) {
		$nsdc = "/usr/sbin/nsd-control";
	} else {
		$nsdc = "/usr/sbin/nsdc";
	}

	if ($action === 'master_fix') {
		exec_with_all_closed("{$nsdc} rebuild; {$nsdc} reload; {$nsdc} notify");
	} elseif ($action === 'master_update') {
		exec_with_all_closed("{$nsdc} rebuild; {$nsdc} reload; {$nsdc} notify");
	}

//	exec_with_all_closed("{$nsdc} update; {$nsdc} reload");
	exec_with_all_closed("/etc/init.d/nsd restart");
*/

	createRestartFile("restart-dns");


