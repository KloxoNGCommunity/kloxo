<?php
	$path = "/opt/configs/djbdns/conf/master";
	$dirs = glob("{$path}/*");

	$datadir = "/opt/configs/djbdns/tinydns/root";
	$datafile = "{$datadir}/data";

	exec("echo '' > {$datafile}");

	foreach ($dirs as $d) {
		exec("cat {$d} >> {$datafile}");
	}

	exec("cd {$datadir}; make");

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

