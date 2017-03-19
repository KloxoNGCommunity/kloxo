<?php

include_once "lib/html/include.php"; 

$domain = (isset($list['domain'])) ? $list['domain'] : null;

echo "*** Deleting domain ini for Rainloop webmail ***\n";

if ($domain) {
	@exec("'rm' -f /home/kloxo/httpd/webmail/rainloop/data/*/_default_/domains/{$domain}.ini");
	echo "- '{$domain}.ini' deleted\n";
}