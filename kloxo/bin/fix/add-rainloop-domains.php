<?php

include_once "lib/html/include.php"; 

$dirs = glob("/home/kloxo/httpd/webmail/rainloop/data/*/_default_/domains", GLOB_MARK);

if (!isset($dirs[0])) { return; }

/*
$tpl = <<<EOF
imap_host = "__mailserver__"
imap_port = 993
imap_secure = "SSL"
smtp_host = "__mailserver__"
smtp_port = 465
smtp_secure = "SSL"
smtp_auth = On
EOF;
*/

$tpl = <<<EOF
imap_host = "127.0.0.1"
imap_port = 993
imap_secure = "SSL"
smtp_host = "127.0.0.1"
smtp_port = 587
smtp_secure = "TLS"
smtp_auth = On
EOF;

$list = parse_opt($argv);

$domain = (isset($list['domain'])) ? $list['domain'] : null;

echo "*** Creating domain ini for Rainloop webmail ***\n";

if ($domain) {
//	$t = $tpl;
//	$r = str_replace('__mailserver__', "mail." . $domain, $t);
	$f = "{$dirs[0]}/{$domain}.ini";
//	file_put_contents($f, $r);
	file_put_contents($f, $tpl);
	chown($f, 'apache');
	chgrp($f, 'apache');
	echo "- '{$domain}.ini' created\n";
} else {
	$pass = slave_get_db_pass();
	$con = new mysqli("localhost", "root", $pass);
	$con->select_db("kloxo");

	$result = $con->query("SELECT nname, remotelocalflag FROM mmail");

	$n = array();

	while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
		if ($row['remotelocalflag'] !== 'remote') {
			$s = $row['nname'];
		//	$t = $tpl;
		//	$r = str_replace('__mailserver__', "mail." . $row['nname'], $t);
			$f = "{$dirs[0]}/{$s}.ini";
		//	file_put_contents($f, $r);
			file_put_contents($f, $tpl);
			chown($f, 'apache');
			chgrp($f, 'apache');
			echo "- '{$s}.ini' created\n";
		}
	}
}
