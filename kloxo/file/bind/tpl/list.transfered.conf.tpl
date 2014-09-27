<?php
	if (array_keys($ips)) {
		$file = "/opt/configs/bind/conf/defaults/named.acl.conf";

		$i = implode(";\n    ", $ips);

		$text = "acl allow-transfer {\n    localhost;\n    {$i};\n};\n\n";

		$text .= "acl allow-notify {\n    {$i};\n};\n";

		file_put_contents($file, $text);
	}
?>