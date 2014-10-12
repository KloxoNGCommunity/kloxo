<?php
	$file = "/opt/configs/yadifa/conf/defaults/yadifa.acl.conf";

	$text  ="<acl>\n";

	if (array_keys($ips)) {
		$i = implode(", ", $ips);

		$text .="    transferer  key allower\n";
		$text .="    admins      localhost\n";
		$text .="    slave       {$i}\n";
	} else {
		$text .="    transferer  key allower\n";
		$text .="    admins      localhost\n";
		$text .="    slave       localhost\n";
	}

	$text .="</acl>\n";

	file_put_contents($file, $text);

	// MR -- because the same structure with nsd and yadifa, so use nsd data

	$path = "/opt/configs/yadifa/conf";

	$dirs = array('master', 'slave', 'reverse');

	foreach ($dirs as $k => $v) {
		if (file_exists("{$path}/{$v}")) {
			exec("'rm' -rf {$path}/{$v}");
		}
	}

