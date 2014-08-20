<?php
	if (array_keys($ip)) {
		$dir = "/opt/configs/djbdns/axfrdns";

		$file = "{$dir}/tcp";

		$text = implode(":allow\n", $ip);

		$text = $text . ":allow\n:deny\n";

		file_put_contents($file, $text);

		$nameduser = "axfrdns";

		chown("{$dir}/data", $nameduser);

		exec_with_all_closed("cd {$dir}; make");
	}
?>

