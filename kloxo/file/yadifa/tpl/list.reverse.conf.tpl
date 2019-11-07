<?php
	$ypath = "/opt/configs/yadifa/conf/defaults";

	exec("echo '' > {$ypath}/yadifa.reverse.conf");

	$d1names = $arpas;

	// MR -- use nsd data because the same structure
	$tpath = "/opt/configs/nsd/conf/reverse";
	$d2files = glob("{$tpath}/*");

	if (empty($d2files)) { return; }

	foreach ($d2files as $k => $v) {
		$d2names[] = str_replace("{$tpath}/", '', $v);
	}

	$d2olds = array_diff($d2names, $d1names);

	// MR -- delete unwanted files
	if (!empty($d2olds)) {
		foreach ($d2olds as $k => $v) {
			unlink("{$tpath}/{$v}");
		}
	}

	$str = '';

	foreach ($d1names as $k => $v) {
		$zone  = "<zone>";
		$zone .= "\n    domain              {$v}";
		$zone .= "\n    type                master";
		$zone .= "\n    file-name           reverse/{$v}";
	//	$zone .= "\n    #allow-transfer     slave";
	//	$zone .= "\n    #allow-notity       slave";
		$zone .= "\n</zone>\n\n";

		$str .= $zone;
	}

	$file = "{$ypath}/yadifa.reverse.conf";

	file_put_contents($file, $str);

