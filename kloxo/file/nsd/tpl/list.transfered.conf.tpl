<?php
	$file = "/opt/configs/nsd/conf/defaults/nsd.acl.conf";

	$text = '';

	if (array_keys($ips)) {
		foreach ($ips as $k => $v) {
			$text .= "    notify: {$v} NOKEY\n    provide-xfr: {$v} NOKEY\n";
			$text .= "\n";
		//	$text .= "    allow-notify: {$v} NOKEY\n";
		//	$text .= "\n";
		}
	}

	file_put_contents($file, $text);
?>
