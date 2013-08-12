<?php
	$file = "/home/bind/conf/defaults/named.acl.conf";

	$text = implode(";    \n", $ip);

	$text = "acl allow-transfer {\n    {$text};\n}\n";

	file_put_contents($file, $text);
?>