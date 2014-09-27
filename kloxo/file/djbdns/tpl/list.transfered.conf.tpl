<?php
	$dir = "/opt/configs/djbdns/axfrdns";

	// MR -- importance if not active
	if (!file_exists($dir)) { return; }

	if (array_keys($ips)) {
		$file = "{$dir}/tcp";

		$i = implode(":allow\n", $ips);

		$text = $i . ":allow\n:deny\n";

		file_put_contents($file, $text);

		$nameduser = "axfrdns";

		chown("{$dir}/data", $nameduser);

		exec_with_all_closed("cd {$dir}; make");
	}
?>

