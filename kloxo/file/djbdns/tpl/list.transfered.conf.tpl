<?php
	if (array_keys($ip)) {
		$file = "/home/djbdns/axfrdns/tcp";

		$text = implode(":allow\n", $ip);

		$text = $text . ":allow\n:deny\n";

		file_put_contents($file, $text);

		$dir = "/home/djbdns/axfrdns";
		$nameduser = "axfrdns";

		chown("{$dir}/data", $nameduser);

		exec_with_all_closed("cd {$dir}; make");
	}
?>

