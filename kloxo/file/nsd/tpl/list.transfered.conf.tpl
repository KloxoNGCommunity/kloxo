<?php
	exec("chown -R nsd:nsd /var/lib/nsd");

	$file = "/opt/configs/nsd/conf/defaults/nsd.acl.conf";

	$text = '';

	if (array_keys($ips)) {
		foreach ($ips as $k => $v) {
			$text .= "    notify: {$v} NOKEY\n";
			$text .= "    provide-xfr: {$v} NOKEY\n";
			$text .= "\n";
		}
	} else {
		exec("echo '' > {$file}");
	}

	file_put_contents($file, $text);
?>
