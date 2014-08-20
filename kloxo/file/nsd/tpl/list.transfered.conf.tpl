<?php
	$file = "/opt/configs/nsd/conf/defaults/nsd.acl.conf";

	$text = '';

	if (array_keys($ip)) {
		foreach ($ip as $k => $v) {
			$text .= "    notify: {$v} NOKEY\n    provide-xfr: {$v} NOKEY\n";
		}
	}

	file_put_contents($file, $text);
?>