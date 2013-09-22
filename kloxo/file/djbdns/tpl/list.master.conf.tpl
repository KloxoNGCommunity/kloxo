<?php
	$path = "/home/djbdns/conf/master";
	$dirs = glob("{$path}/*");

	$datadir = "/home/djbdns/tinydns/root";
	$datafile = "/home/djbdns/tinydns/root/data";

	exec("echo '' > {$datafile}");

	foreach ($dirs as $d) {
		exec("cat {$d} >> {$datafile}");
	}

	exec("cd {$datadir}; make");

	if ($action === 'fix') {
	//	exec("/etc/init.d/djbdns restart");
	//	exec_with_all_closed("/etc/init.d/djbdns restart >/dev/null 2>&1 &");
		createRestartFile("djbdns");
	}

	if ($action === 'update') {
		foreach ($domains as $k => $v) {
			exec_with_all_closed("sh /script/dnsnotify {$v}");
		}
	}
?>

