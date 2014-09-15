<?php
	$dir = "/opt/configs/djbdns/axfrdns";

	// MR -- importance if not active
	if (!file_exists($dir)) { return; }

	if (array_keys($ip)) {
		$file = "{$dir}/tcp";

		$text = implode(":allow\n", $ip);

		$text = $text . ":allow\n:deny\n";

		file_put_contents($file, $text);

		$nameduser = "axfrdns";

		chown("{$dir}/data", $nameduser);

		exec_with_all_closed("cd {$dir}; make");
	}
?>

