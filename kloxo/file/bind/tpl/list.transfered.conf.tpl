<?php
	if (array_keys($ip)) {
		$file = "/opt/configs/bind/conf/defaults/named.acl.conf";

		$text = implode(";\n    ", $ip);

		$text = "acl allow-transfer {\n    localhost;\n    {$text};\n};\n";

		$text .= "acl allow-notify {\n    {$text};\n};\n";

		file_put_contents($file, $text);
	}
?>