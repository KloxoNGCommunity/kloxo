<?php

include_once "lib/html/include.php"; 

$dirs = glob("/home/kloxo/httpd/webmail/rainloop/data/*/_default_/domains", GLOB_MARK);

$tpl = <<<EOF
imap_host = "__mailserver__"
imap_port = 993
imap_secure = "SSL"
smtp_host = "__mailserver__"
smtp_port = 465
smtp_secure = "SSL"
smtp_auth = On
EOF;

$pass = slave_get_db_pass();
$con = new mysqli("localhost", "root", $pass);
$con->select_db("kloxo");

$result = $con->query("SELECT nname, remotelocalflag FROM mmail");

$n = array();

echo "*** Creating domain ini for Rainloop webmail ***\n";

while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
	if ($row['remotelocalflag'] !== 'remote') {
		$s = $row['nname'];
		$t = $tpl;
		$r = str_replace('__mailserver__', "mail." . $row['nname'], $t);
		$f = "{$dirs[0]}/{$s}.ini";
		file_put_contents($f, $r);
		chown($f, 'apache');
		chgrp($f, 'apache');
		echo "- Creating '{$s}.ini'\n";
	}
}
