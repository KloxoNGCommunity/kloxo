<?php
	$file = "/opt/configs/yadifa/conf/defaults/yadifa.acl.conf";

	if (array_keys($ips)) {
		$i = implode(" ", $ips);

		$text  ="    allow-notify    {$i}\n";
		$text .="    allow-transfer  {$i}\n";

		file_put_contents($file, $text);
	} else {
		exec("echo '' > {$file}");
	}

	// MR -- because the same structure with nsd and yadifa, so use nsd data

	$path = "/opt/configs/yadifa/conf";

	$dirs = array('master', 'slave', 'reverse');

	foreach ($dirs as $k => $v) {
		if (file_exists("{$path}/{$v}")) {
			exec("'rm' -rf {$path}/{$v}");
		}
	}

        #allow-update                none
        #allow-transfer              none
        #allow-notify                none
