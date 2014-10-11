<?php
	exec("chown -R nsd:nsd /var/lib/nsd");
	exec("chmod 0777 /opt/configs/nsd/conf/master; chmod 0777 /opt/configs/nsd/conf/slave");

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
