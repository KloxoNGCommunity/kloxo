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
?>

