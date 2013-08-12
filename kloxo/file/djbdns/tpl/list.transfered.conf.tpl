<?php
	$file = "/home/djbdns/axfrdns/tcp";

	$text = implode(":allow\n", $ip);

	$text = $text . ":allow\n:deny\n";

	file_put_contents($file, $text);

	$dir = "/home/djbdns/axfrdns";
	$nameduser = "axfrdns";

	chown("{$dir}/data", $nameduser);
	exec("cd {$dir}; make");
?>

