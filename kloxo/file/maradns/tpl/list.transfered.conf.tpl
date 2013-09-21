<?php
	if (array_keys($ip)) {
		$str = implode(",", $ip);
		$str = "zone_transfer_acl = \"{$str}\"\n";
	}

	$file = "/etc/mararc";

	$srctxt = file_get_contents($file);

	$startin = "### begin - acl - do not remove/modify this line\n";
	$endin = "### end - acl - do not remove/modify this line";

	// MR -- calling function in lib.php
	$content = replace_between($srctxt, $startin, $endin, $str);

	file_put_contents($file, $content);
?>