<?php
	$path = "/home/phpcfg/fpm";

	$dirs = glob("{$path}/pool/{$fpm_type}*.conf");

	$datafile = "{$path}/conf/{$fpm_type}.conf";

	if (strpos($fpm_type, '52') !== false) {
		exec("echo '<?xml version=\"1.0\">\n<configuration>' > {$datafile}");
	} else {
		exec("echo '' > {$datafile}");
	}

	$g = file_get_contents("{$path}/pool/_{$fpm_type}-global.conf");

	exec("echo '{$g}' >> {$datafile}");

	foreach ($dirs as $d) {
		exec("cat {$d} >> {$datafile}");
	}

	if (strpos('52', $fpm_type) !== false) {
		exec("echo '</configuration>' > {$datafile}");
	}
?>
