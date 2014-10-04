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

	
	$datafile = "{$datadir}/master";

//	exec("echo '' > {$datafile}");
	exec("'rm' -f {$datafile}");

	foreach ($dirs as $d) {
		exec("cat {$d} >> {$datafile}");
	}
