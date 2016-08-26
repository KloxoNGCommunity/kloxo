<?php

include_once "lib/html/include.php"; 

$dirs = glob("/home/kloxo/httpd/webmail/rainloop/data/*/_default_/domains", GLOB_MARK);

if (!isset($dirs[0])) { return; }

$list = parse_opt($argv);

$domain = (isset($list['domain'])) ? $list['domain'] : null;

echo "*** Deleting domain ini for Rainloop webmail ***\n";

if ($domain) {
	$t = $tpl;
	$r = str_replace('__mailserver__', "mail." . $domain, $t);
	$f = "{$dirs[0]}/{$domain}.ini";
	@exec("'rm' -f {$f}");
	echo "- '{$domain}.ini' deleted\n";
}