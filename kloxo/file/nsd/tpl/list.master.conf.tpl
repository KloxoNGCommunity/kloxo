<?php
	$path = "/home/nsd/conf/master";
	$dirs = glob("{$path}/*");

	$str = '';

	foreach ($dirs as $d) {
		$d = str_replace("{$path}/", "", $d);
		$zone  = "zone:\n    name: {$d}\n    zonefile: master/{$d}\n";

		$zone .= "    include: \"/home/nsd/conf/defaults/nsd.acl.conf\"\n";
		
		$str .= $zone . "\n";
	}

	$file = "/home/nsd/conf/defaults/nsd.master.conf";

	file_put_contents($file, $str);

	if ($action === 'fix') {
		exec_with_all_closed("nsdc rebuild; nsdc reload; nsdc notify");
	} elseif ($action === 'update') {
		exec_with_all_closed("nsdc rebuild; nsdc reload; nsdc notify");
	}
?>

