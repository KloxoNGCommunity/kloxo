<?php
	$datadir = "/opt/configs/djbdns/tinydns/root";

	// MR -- importance if not active
	if (!file_exists($datadir)) { return; }

	if (file_exists("/opt/djbdns/bin/tinydns-data")) {
		exec("echo 'data.cdb: data\n\t/opt/djbdns/bin/tinydns-data' > " .
			"/opt/configs/djbdns/tinydns/root/Makefile");
	} elseif (file_exists("/bin/tinydns-data")) {
		exec("echo 'data.cdb: data\n\t//bin/tinydns-data' > " .
			"/opt/configs/djbdns/tinydns/root/Makefile");
	} else {
		return;
	}

	$path = "/opt/configs/djbdns/conf/master";
	$dirs = glob("{$path}/*");

	
	$datafile = "{$datadir}/data";

	exec("echo '' > {$datafile}");

	foreach ($dirs as $d) {
		exec("cat {$d} >> {$datafile}");
	}

	exec("cd {$datadir}; make");

	if (!file_exists("/etc/rc.d/init.id/tinydns")) { return; }

	if ($action === 'fix') {
		if (array_keys($domains)) {
			exec_with_all_closed("/etc/init.d/djbdns reload");

			foreach ($domains as $k => $v) {
				exec_with_all_closed("sh /script/dnsnotify {$v}");
			}
		}
	} elseif ($action === 'update') {
		exec_with_all_closed("/etc/init.d/djbdns reload; sh /script/dnsnotify {$domain}");
	}
?>

