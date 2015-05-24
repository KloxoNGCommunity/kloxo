<?php

// by mustafa@bigraf.com for Kloxo-MR

include_once "lib/html/include.php";
initProgram('admin');

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

$phpmdirs = glob("/opt/php*m", GLOB_MARK);

$phpsbranch = str_replace("\n", "", file_get_contents("/usr/local/lxlabs/kloxo/init/kloxo_php_active"));
$phpsver = str_replace("\n", "", file_get_contents("/opt/{$phpsbranch}/version"));

if (file_exists("/usr/local/lxlabs/kloxo/init/kloxo_use_php-cgi")) {
	$phpsver = $phpsver . " (cgi mode)";
} else {
	$phpsver = $phpsver . " (fpm mode)";
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

$hiawathabranch = getRpmBranchInstalled('hiawatha');
if ($hiawathabranch) {
	exec("rpm -q {$hiawathabranch}", $apphiawatha);
	$apphiawatha = trim($apphiawatha[0]);
	$kloxohiawatha = $apphiawatha;

	exec("chkconfig --list|grep 'hiawatha'|grep ':on'", $out);

	if ($out[0] !== '') {
	//	$apphiawatha .= " (also as webserver)";
		$apphiawatha = "--unused--";
	}
} else {
	$apphiawatha = '--uninstalled--';
}

$cachebranch = getRpmBranchInstalled('webcache');
if ($cachebranch) {
	exec("rpm -q {$cachebranch}", $appcache);
	$appcache = trim($appcache[0]);
} else {
	$appcache = '--uninstalled--';
}

$qmailbranch = getRpmBranchInstalled('qmail-toaster');
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

$courierimapbranch = 'courier-imap-toaster';
$isinstalled = isRpmInstalled($courierimapbranch);
if ($isinstalled) {
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

$a = get_namelist_from_objectlist($login->getList('pserver'), 'syncserver');
$b = implode("", $a);

$phptype = db_get_value('serverweb', "pserver-{$b}", 'php_type');

exec("free -m", $meminfo);

echo "\n";
echo "A. Kloxo-MR: " . $kloxomrver . "\n";
echo "   - Web: " . $kloxohiawatha . "\n";
echo "   - PHP: " . $phpsbranch . "-" . $phpsver . "\n";
echo "\n";
echo "B. OS: " . $osrelease[0] . " " . $osplateform[0] . "\n";
echo "\n";
echo "C. Apps:\n";
echo "   1. MySQL: " .  $appmysql . "\n";
echo "   2. PHP: \n";
echo "      - Branch: " .  $appphp . "\n";
echo "      - Multiple: \n";
foreach ($phpmdirs as $k => $v) {
	$v1 = str_replace("/", "", str_replace("/opt/", "", $v));
	$v2  = file_get_contents($v . "/version");
	echo "        * " . $v1 . "-" . str_replace("\n", "", $v2) . "\n";
}
echo "   3. Httpd: " .  $apphttpd . "\n";
echo "      - PHP Type: " . $phptype . "\n";
echo "   4. Lighttpd: " .  $applighttpd . "\n";
echo "   5. Hiawatha: " .  $apphiawatha . "\n";
echo "   6. Nginx: " .  $appnginx . "\n";
echo "   7. Cache: " .  $appcache . "\n";
echo "   8. Dns: " .  $appdns . "\n";
echo "   9. Qmail: " .  $appqmail . "\n";

if ($appdovecot !== '--uninstalled--') {
	echo "      - with: " . $appdovecot  . "\n";
}
if ($appcourierimap !== '--uninstalled--') {
	echo "      - with: " . $appcourierimap  . "\n";
}

echo "\n";
echo "D. Memory:\n";
echo "   " . $meminfo[0] . "\n";
echo "   " . $meminfo[1] . "\n";
echo "   " . $meminfo[2] . "\n";
echo "   " . $meminfo[3] . "\n";
echo "\n";

