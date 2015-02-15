<?php
	if (file_exists("/etc/rndc.conf")) {
		exec("'rm' -f /etc/rndc.conf");
	}

	if (!file_exists("/var/log/named")) {
		exec("mkdir -p /var/log/named");
	}

	exec("sed -i 's/rndckey/rndc-key/' /etc/rndc.key");

	$file = "/opt/configs/bind/conf/defaults/named.acl.conf";

	if (array_keys($ips)) {
		$i = implode(";\n    ", $ips);

		$text = "acl allow-transfer {\n    localhost;\n    {$i};\n};\n\n";

		$text .= "acl allow-notify {\n    {$i};\n};\n";
	} else {
		$text = "acl allow-transfer {\n    localhost;\n};\n\n";
		$text .= "acl allow-notify {\n    localhost;\n};\n";
	}

	file_put_contents($file, $text);

	// MR -- because the same structure with nsd and yadifa, so use nsd data

	$path = "/opt/configs/bind/conf";

	$dirs = array('master', 'slave', 'reverse');

	foreach ($dirs as $k => $v) {
		if (file_exists("{$path}/{$v}")) {
			exec("'rm' -rf {$path}/{$v}");
		}
	}

