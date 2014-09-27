<?php
	$path = "/opt/configs/bind/conf/master";
	$dirs = glob("{$path}/*");

	$str = '';

	foreach ($dirs as $d) {
		$d = str_replace("{$path}/", "", $d);
		$zone = "zone \"{$d}\" {\n    type master;\n    file \"master/{$d}\";\n};\n";
		$str .= $zone;
	}

	$file = "/opt/configs/bind/conf/defaults/named.master.conf";

	file_put_contents($file, $str);

	if (!file_exists("/etc/rc.d/init.id/named")) { return; }

/*
//	if ($target === 'master') {
		if ($action === 'fix') {
			if (array_keys($domains)) {
				foreach ($domains as $k => $v) {
					exec_with_all_closed("rndc reload {$v}; rndc notify {$v}");
				}
			} else {
				exec_with_all_closed("rndc reconfig");
			}
		} elseif ($action === 'update') {
			exec_with_all_closed("rndc reload {$domain}; rndc notify {$domain}");
		}
//	}
*/
	createRestartFile("restart-dns");
?>