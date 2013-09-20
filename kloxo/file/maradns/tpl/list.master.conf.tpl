<?php
	$path = "/home/maradns/conf/master";
	$dirs = glob("{$path}/*");

	$str = '';

	foreach ($dirs as $d) {
		$d = str_replace("{$path}/", "", $d);
		$zone = "csv2[\"{$d}.\"] = \{$d}\"\n";
		$str .= $zone;
	}

	if (file_exists("/home/maradns/etc/custom.mararc")) {
		$srctxt = file_get_contents("/home/maradns/etc/custom.mararc");
	} else {
		$srctxt = file_get_contents("/home/maradns/etc/mararc");
	}

	$startin = "\n### begin - zone list - do not remove/modify this line\n";
	$endin = "### end - zone list - do not remove/modify this line\n";

	$content = $srctxt . $startin . $str . $endin;

	file_put_contents("/etc/mararc", $content);

	if ($action !== 'fix') {
		exec("service maradns restart");
	}
?>