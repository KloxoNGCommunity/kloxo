<?php
	$ypath = "/opt/configs/yadifa";
	$npath = "/opt/configs/nsd";

	$file = "{$ypath}/conf/defaults/yadifa.acl.conf";

	$text  ="<acl>\n";

	if (array_keys($ips)) {
		$i = implode(", ", $ips);
		$text .= "    slave       {$i}\n";
		$yfile = getLinkCustomfile("{$ypath}/etc", "yadifad.conf");
	} else {
		$text .= "    ## no info for slave\n";
		$yfile = getLinkCustomfile("{$ypath}/etc", "yadifad_noslave.conf");
	}

	$text .="</acl>\n\n";

	file_put_contents($file, $text);

	// MR -- then merge files because trouble with 'include'

	$afile = $file;
	$mfile = "{$ypath}/conf/defaults/yadifa.master.conf";
	$sfile = "{$ypath}/conf/defaults/yadifa.slave.conf";
	$rfile = "{$ypath}/conf/defaults/yadifa.reverse.conf";

	exec("cat {$yfile} {$afile} {$mfile} {$sfile} {$rfile} > /etc/yadifad.conf");

	// MR -- because the same structure with nsd and yadifa, so use nsd data
	$cpath = "{$ypath}/conf";

	$dirs = array('master', 'slave', 'reverse');

	foreach ($dirs as $k => $v) {
		if (file_exists("{$cpath}/{$v}")) {
			exec("'rm' -rf {$cpath}/{$v}");
		}
	}

