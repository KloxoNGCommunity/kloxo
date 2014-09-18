<?php
	$path = "/opt/configs/nsd/conf/master";
	$dirs = glob("{$path}/*");

	$str = '';

	foreach ($dirs as &$d) {
		$d = str_replace("{$path}/", "", $d);
		$zone  = "zone:\n    name: {$d}\n    zonefile: master/{$d}\n";

	//	$zone .= "    include: \"/opt/configs/nsd/conf/defaults/nsd.acl.conf\"\n";

		if (array_keys($ips)) {
			foreach ($ips as $k => $v) {
				$zone .= "    notify: {$v} NOKEY\n    provide-xfr: {$v} NOKEY\n";
			}
		}
		
		$str .= $zone . "\n";
	}

	$file = "/opt/configs/nsd/conf/defaults/nsd.master.conf";

	file_put_contents($file, $str);

	if (!file_exists("/etc/rc.d/init.id/nsd")) { return; }

	if (file_exists("usr/sbin/nsd-control")) {
		$nsdc = "usr/sbin/nsd-control";
	} else {
		$nsdc = "usr/sbin/nsdc";
	}

	if ($action === 'fix') {
		exec_with_all_closed("{$nsdc} rebuild; {$nsdc} reload; {$nsdc} notify");
	} elseif ($action === 'update') {
		exec_with_all_closed("{$nsdc} rebuild; {$nsdc} reload; {$nsdc} notify");
	}

//	exec_with_all_closed("/etc/init.d/nsd restart");
	
?>

