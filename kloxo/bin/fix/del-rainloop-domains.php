<?php

include_once "lib/html/include.php"; 

$dirs = glob("/home/kloxo/httpd/webmail/rainloop/data/*/_default_/domains", GLOB_MARK);

if (!isset($dirs[0])) { return; }

$tpl = <<<EOF
imap_host = "__mailserver__"
imap_port = 993
imap_secure = "SSL"
smtp_host = "__mailserver__"
smtp_port = 465
smtp_secure = "SSL"
smtp_auth = On
EOF;

$list = parse_opt($argv);

$domain = (isset($list['domain'])) ? $list['domain'] : null;

echo "*** Creating domain ini for Rainloop webmail ***\n";

if ($domain) {
	$t = $tpl;
	$r = str_replace('__mailserver__', "mail." . $domain, $t);
	$f = "{$dirs[0]}/{$domain}.ini";
	@exec("'rm' -f {$f}");
	echo "- Deleting '{$domain}.ini'\n";
}