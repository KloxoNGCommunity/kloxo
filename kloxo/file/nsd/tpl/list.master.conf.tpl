<?php
	$path = "/opt/configs/nsd/conf/master";
	$dirs = glob("{$path}/*");

	$str = '';

	foreach ($dirs as $d) {
		$d = str_replace("{$path}/", "", $d);
		$zone  = "zone:\n    name: {$d}\n    zonefile: master/{$d}\n";

		$zone .= "    include: \"/opt/configs/nsd/conf/defaults/nsd.acl.conf\"\n";
		
		$str .= $zone . "\n";
	}

	$file = "/opt/configs/nsd/conf/defaults/nsd.master.conf";

	file_put_contents($file, $str);

	if ($action === 'fix') {
		exec_with_all_closed("nsdc rebuild; nsdc reload; nsdc notify");
	} elseif ($action === 'update') {
		exec_with_all_closed("nsdc rebuild; nsdc reload; nsdc notify");
	}

//	exec_with_all_closed("/etc/init.d/nsd restart");
	
?>

