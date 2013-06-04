<?php

// by mustafa@bigraf.com for Kloxo-MR

include_once "htmllib/lib/include.php";

exec("sh /script/version --vertype=full", $kloxomrver);
$kloxomrver = $kloxomrver[0];

exec("cat /etc/*release", $osrelease);
exec("uname -m", $osplateform);

$mysqlbranch = getRpmBranchInstalled('mysql');
if ($mysqlbranch) {
	exec("rpm -q {$mysqlbranch}", $appmysql);
	$appmysql = trim($appmysql[0]);
} else {
	$appmysql = '--uninstalled--';
}

$phpbranch = getRpmBranchInstalled('php');
if ($phpbranch) {
	exec("rpm -q {$phpbranch}", $appphp);
	$appphp = trim($appphp[0]);
} else {
	$appphp = '--uninstalled--';
}

$httpdbranch = getRpmBranchInstalled('httpd');
if ($httpdbranch) {
	exec("rpm -q {$httpdbranch}", $apphttpd);
	$apphttpd = trim($apphttpd[0]);
} else {
	$apphttpd = '--uninstalled--';
}

$lighttpdbranch = getRpmBranchInstalled('lighttpd');
if ($lighttpdbranch) {
	exec("rpm -q {$lighttpdbranch}", $applighttpd);
	$applighttpd = trim($applighttpd[0]);
} else {
	$applighttpd = '--uninstalled--';
}

$nginxbranch = getRpmBranchInstalled('nginx');
if ($nginxbranch) {
	exec("rpm -q {$nginxbranch}", $appnginx);
	$appnginx = trim($appnginx[0]);
} else {
	$appnginx = '--uninstalled--';
}

$qmailbranch = getRpmBranchInstalled('qmail');
if ($qmailbranch) {
	exec("rpm -q {$qmailbranch}", $appqmail);
	$appqmail = trim($appqmail[0]);
} else {
	$appqmail = '--uninstalled--';
}

$dovecotbranch = getRpmBranchInstalled('dovecot');
if ($dovecotbranch) {
	exec("rpm -q {$dovecotbranch}", $appdovecot);
	$appdovecot = trim($appdovecot[0]);
} else {
	$appdovecot = '--uninstalled--';
}

$courierimapbranch = isRpmInstalled('courier-imap-toaster');
if ($courierimapbranch) {
	exec("rpm -q {$courierimapbranch}", $appcourierimap);
	$appcourierimap = trim($appcourierimap[0]);
} else {
	$appcourierimap = '--uninstalled--';
}

$dnsbranch = getRpmBranchInstalled('dns');
if ($dnsbranch) {
	exec("rpm -q {$dnsbranch}", $appdns);
	$appdns = trim($appdns[0]);
} else {
	$appdns = '--uninstalled--';
}

$sq = new Sqlite(null, 'serverweb');
$res = $sq->getRowsWhere("nname = 'pserver-localhost'", array('php_type'));
$phptype = $res[0];

exec("free -m", $meminfo);

echo "\n";
echo "A. Kloxo-MR: " . $kloxomrver . "\n";
echo "\n";
echo "B. OS: " . $osrelease[0] . " " . $osplateform[0] . "\n";
echo "\n";
echo "C. Apps:\n";
echo "   1. MySQL: " .  $appmysql . "\n";
echo "   2. PHP: " .  $appphp . "\n";
echo "   3. Httpd: " .  $apphttpd . "\n";
echo "   4. Lighttpd: " .  $applighttpd . "\n";
echo "   5. Nginx: " .  $appnginx . "\n";
echo "   6. Qmail: " .  $appqmail . "\n";

if ($appdovecot !== '--uninstalled--') {
	echo "      - with: " . $appdovecot  . "\n";
}
if ($appcourierimap !== '--uninstalled--') {
	echo "      - with: " . $appcourierimap  . "\n";
}

echo "   7. Dns: " .  $appdns . "\n";
echo "\n";
echo "D. Php-type (for Httpd/proxy): " . $phptype['php_type'] . "\n";
echo "\n";
echo "E. Memory:\n";
echo "   " . $meminfo[0] . "\n";
echo "   " . $meminfo[1] . "\n";
echo "   " . $meminfo[2] . "\n";
echo "   " . $meminfo[3] . "\n";
echo "\n";


