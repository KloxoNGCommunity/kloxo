<?php
	$path = "/home/maradns/conf/master";
	$dirs = glob("{$path}/*");

	$str = '';

	foreach ($dirs as $d) {
		$d = str_replace("{$path}/", "", $d);
		$zone = "csv2[\"{$d}.\"] = \"{$d}\"\n";
		$str .= $zone;
	}

	$file = "/etc/mararc";

	$srctxt = file_get_contents($file);

	$startin = "### begin - zone - do not remove/modify this line\n";
	$endin = "### end - zone - do not remove/modify this line";

	// MR -- calling function in lib.php
	$content = replace_between($srctxt, $startin, $endin, $str);

	file_put_contents($file, $content);

	if ($action === 'fix') {
		// MR -- execute here becuase very slow!
	//	exec("/etc/init.d/maradns restart");
	//	exec_with_all_closed("/etc/init.d/maradns restart >/dev/null 2>&1 &");
		createRestartFile("maradns");
	}
?>