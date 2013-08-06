<?php
	$path = "/home/djbdns/conf/master";
	$dirs = glob("{$path}/*");

	$file = "/home/djbdns/tinydns/root/data";
	unlink($file);

	foreach ($dirs as $d) {
	//	$data = file_get_contents($d);
	//	file_put_contents($file, $data, FILE_APPEND);

		exec("cat {$d} >> {$file}");
	}
?>

