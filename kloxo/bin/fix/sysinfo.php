<?php

// by mustafa@bigraf.com for Kloxo-MR

include_once "lib/html/include.php";
initProgram('admin');

$kloxopath="/usr/local/lxlabs/kloxo";

exec("sh /script/version --vertype=full", $kloxomrver);
$kloxomrver = $kloxomrver[0];

exec("cat /etc/*release", $osrelease);
exec("uname -m", $osplateform);

$out = null;

$mysqlbranch = getRpmBranchInstalled('mysql');
if ($mysqlbranch) {
	exec("rpm -qa {$mysqlbranch}", $out);
	$appmysql = trim($out[0]);
} else {
	$appmysql = '--uninstalled--';
}

$out = null;

$phpbranch = getRpmBranchInstalled('php');
if ($phpbranch) {
	exec("rpm -qa {$phpbranch}-cli", $out);
	$appphp = trim($out[0]);
} else {
	$appphp = '--uninstalled--';
}

$out = null;

$phpmdirs = glob("/opt/php*m", GLOB_MARK);

$phpsbranch = str_replace("\n", "", file_get_contents("{$kloxopath}/init/kloxo_php_active"));
$phpsver = str_replace("\n", "", file_get_contents("/opt/{$phpsbranch}/version"));

if (file_exists("{$kloxopath}/init/kloxo_use_php-cgi")) {
	$phpsver = $phpsver . " (cgi mode)";
} else {
	$phpsver = $phpsver . " (fpm mode)";
}

$appApache = '--uninstalled--';
exec("cat /usr/local/lxlabs/kloxo/etc/list/web.lst|tr ',' '\n'|awk -F\"_\" '{print $1}'|" .
	"grep httpd|grep -v 'nginx\|tengine'|grep -v lighttpd|grep -v hiawatha", $apachelist);

$out = null;
foreach ($apachelist as $k => $v) {
	$apachebranch = getRpmBranchInstalled($v);

	if ($apachebranch) {
		exec("rpm -qa {$apachebranch}", $out);
		$appApache = trim($out[0]);
	}
}

$appLighttpd = '--uninstalled--';
exec("cat /usr/local/lxlabs/kloxo/etc/list/web.lst|tr ',' '\n'|awk -F\"_\" '{print $1}'|" .
	"grep lighttpd|grep -v 'nginx\|tengine'|grep -v hiawatha", $lighttpdlist);

$out = null;
foreach ($lighttpdlist as $k => $v) {
	$lighttpdbranch = getRpmBranchInstalled($v);

	if ($lighttpdbranch) {
		exec("rpm -qa {$lighttpdbranch}", $out);
		$appLighttpd = trim($out[0]);
	}
}

$appNginx = '--uninstalled--';
exec("cat /usr/local/lxlabs/kloxo/etc/list/web.lst|tr ',' '\n'|awk -F\"_\" '{print $1}'|" .
	"grep 'nginx\|tengine'|grep -v httpd|grep -v lighttpd|grep -v hiawatha", $nginxlist);

$out = null;
foreach ($nginxlist as $k => $v) {
	$nginxbranch = getRpmBranchInstalled($v);

	if ($nginxbranch) {
		exec("rpm -qa {$nginxbranch}", $out);
		$appNginx = trim($out[0]);
	}
}

$appHiawatha = '--uninstalled--';
exec("cat /usr/local/lxlabs/kloxo/etc/list/web.lst|tr ',' '\n'|awk -F\"_\" '{print $1}'|" .
	"grep hiawatha|grep -v httpd|grep -v lighttpd|grep -v nginx", $hiawathalist);

$out = null;
foreach ($hiawathalist as $k => $v) {
	$hiawathabranch = getRpmBranchInstalled($v);

	if ($hiawathabranch) {
		exec("rpm -qa {$hiawathabranch}", $out);
		$appHiawatha = trim($out[0]);
	}
}

$out = null;

$atsbranch = getRpmBranchInstalled('trafficserver');
if ($atsbranch) {
	exec("rpm -qa {$atsbranch}", $out);
	$appats = trim($out[0]);
} else {
	$appats = '--uninstalled--';
}

$out = null;

$squidbranch = getRpmBranchInstalled('squid');
if ($squidbranch) {
	exec("rpm -qa {$squidbranch}", $out);
	$appsquid = trim($out[0]);
} else {
	$appsquid = '--uninstalled--';
}

$out = null;

$varnishbranch = getRpmBranchInstalled('varnish');
if ($varnishbranch) {
	exec("rpm -qa {$varnishbranch}", $out);
	$appvarnish = trim($out[0]);
} else {
	$appvarnish = '--uninstalled--';
}

$out = null;

$bindbranch = getRpmBranchInstalled('bind');
if ($bindbranch) {
	exec("rpm -qa {$bindbranch}", $out);
	$appbind = trim($out[0]);
} else {
	$appbind = '--uninstalled--';
}

$out = null;

$djbdnsbranch = getRpmBranchInstalled('djbdns');
if ($djbdnsbranch) {
	exec("rpm -qa {$djbdnsbranch}", $out);
	$appdjbdns = trim($out[0]);
} else {
	$appdjbdns = '--uninstalled--';
}

$out = null;

$nsdbranch = getRpmBranchInstalled('nsd');
if ($nsdbranch) {
	exec("rpm -qa {$nsdbranch}", $out);
	$appnsd = trim($out[0]);
} else {
	$appnsd = '--uninstalled--';
}

$out = null;

$pdnsbranch = getRpmBranchInstalled('pdns');
if ($pdnsbranch) {
	exec("rpm -qa {$pdnsbranch}", $out);
	$apppdns = trim($out[0]);
} else {
	$apppdns = '--uninstalled--';
}

$out = null;

$yadifabranch = getRpmBranchInstalled('yadifa');
if ($yadifabranch) {
	exec("rpm -qa {$yadifabranch}", $out);
	$appyadifa = trim($out[0]);
} else {
	$appyadifa = '--uninstalled--';
}

$out = null;

$qmailbranch = getRpmBranchInstalled('qmail-toaster');
if ($qmailbranch) {
	exec("rpm -qa {$qmailbranch}", $out);
	$appqmail = trim($out[0]);
} else {
	$appqmail = '--uninstalled--';
}

$out = null;

$dovecotbranch = getRpmBranchInstalled('dovecot');
if ($dovecotbranch) {
	exec("rpm -qa {$dovecotbranch}", $out);
	$appdovecot = trim($out[0]);
} else {
	$appdovecot = '--uninstalled--';
}

$out = null;

$courierimapbranch = 'courier-imap-toaster';
$isinstalled = isRpmInstalled($courierimapbranch);
if ($isinstalled) {
	exec("rpm -qa {$courierimapbranch}", $out);
	$appcourierimap = trim($out[0]);
} else {
	$appcourierimap = '--uninstalled--';
}

$out = null;

$a = get_namelist_from_objectlist($login->getList('pserver'), 'syncserver');
$b = implode("", $a);

$phptype = db_get_value('serverweb', "pserver-{$b}", 'php_type');

if (!isset($phptype)) { $phptype = 'php-fpm_event (default)'; }

if (getServiceType('php-fpm') === 'systemd') {
	$seddata = 's:^ExecStart=/usr/sbin/\(.*\) -y \(.*\):\1:';
	exec("cat /usr/lib/systemd/system/php-fpm.service|grep 'ExecStart='|sed -e '" . $seddata . "'", $out);
} else {
	$seddata = 's:^prog=\"\(.*\)\":\1:';
	exec("cat /etc/rc.d/init.d/php-fpm|grep 'prog='|sed -e '" . $seddata . "'", $out);
}
if (count($out) > 0) {
	$phpused = $out[0];

	if ($phpused === "php-fpm") {
		$phpused = '--PHP Branch--';
	}
} else {
	$phpused = '--PHP Branch--';
}

$out = null;

$pop3app = slave_get_driver('pop3');

if ($pop3app === 'courier') { $pop3app = 'courier-imap'; }

exec("rpm -qa {$pop3app}-toaster", $out);

if (count($out) > 0) {
	$pop3app = $out[0];
} else {
	$pop3app = 'none';
}

$out = null;

$smtpapp = slave_get_driver('smtp');

exec("rpm -qa {$smtpapp}-toaster", $out);

if (count($out) > 0) {
	$smtpapp = $out[0];
} else {
	$smtpapp = 'none';
}

$out = null;

$spamapp = slave_get_driver('spam');

if ($spamapp === 'spamassassin') { $spamapp === 'spamassassin-toaster'; }

exec("rpm -qa {$spamapp}", $out);

if (count($out) > 0) {
	$spamapp = $out[0];
} else {
	$spamapp = '--uninstalled--';
}

$out = null;

if (file_exists("/etc/httpd/conf.d/suphp2.conf")) {
	$secondary_php = 'on';
} else {
	$secondary_php = 'off';
}

$out = null;

$ftpbranch = getRpmBranchInstalled('pure-ftpd');
if ($ftpbranch) {
	exec("rpm -qa {$ftpbranch}", $out);
	$appftp = trim($out[0]);
} else {
	$appftp = '--uninstalled--';
}

exec("free -m", $meminfo);

exec("df -h /", $diskinfo);

$gen = $login->getObject('general')->generalmisc_b;
$webstatsprog = ($gen->webstatisticsprogram) ? $gen->webstatisticsprogram : 'awstats';

$out = null;

// MR -- use grep because possible as kloxomr-stats-<statsprog> or just <statsprog>
exec("rpm -qa|grep {$webstatsprog}", $out);

if (count($out) > 0) {
	$appstats = $out[0];
} else {
	$appstats = '--uninstalled--';
}

echo "";
echo "\n";
echo "A. Control Panel:" .
	"               \n"; // need more space because overwrite waiting line
echo "   - Kloxo-MR: {$kloxomrver}\n";
echo "   - Web: {$appHiawatha}\n";
echo "   - PHP: {$phpsbranch} - {$phpsver}\n";
//echo "\n";
echo "B. Plateform:\n";
echo "   - OS: {$osrelease[0]} {$osplateform[0]} \n";
echo "   - Hostname: " . gethostname() . "\n";
//echo "\n";
echo "C. Services:\n";
echo "   1. MySQL: {$appmysql}\n";
echo "   2. PHP: \n";
echo "      - Installed:\n";
echo "        - Branch: {$appphp}\n";
if ($phpmdirs) {
	echo "        - Multiple: \n";
	foreach ($phpmdirs as $k => $v) {
		if (file_exists("{$v}/version")) {
			$v1 = str_replace("/", "", str_replace("/opt/", "", $v));
			$v2  = file_get_contents("{$v}/version");
			echo "          * {$v1} - " . str_replace("\n", "", $v2) . "\n";
		} else {
			// MR - remove if not version file not exists
			exec("'rm' -rf {$v}");
		}
	}
}
echo "      - Used: {$phpused}\n";

if (file_exists("{$kloxopath}/etc/flag/enablemultiplephp.flg")) {
	echo "      - Multiple: enable\n";
} else {
	echo "      - Multiple: disable\n";
}

echo "   3. Web Used: " . slave_get_driver('web') . "\n";
echo "     - Hiawatha: {$appHiawatha}\n";
echo "     - Lighttpd: {$appLighttpd}\n";
echo "     - Nginx: {$appNginx}\n";
echo "     - Apache: {$appApache}\n";
echo "       - PHP Type: {$phptype}\n";
echo "       - Secondary PHP: {$secondary_php}\n";
echo "   4. WebCache: " .  slave_get_driver('webcache') . "\n";
echo "     - ATS: {$appats}\n";
echo "     - Squid: {$appsquid}\n";
echo "     - Varnish: {$appvarnish}\n";
echo "   5. Dns: " .  slave_get_driver('dns') . "\n";
echo "     - Bind: {$appbind}\n";
echo "     - DJBDns: {$appdjbdns}\n";
echo "     - NSD: {$appnsd}\n";
echo "     - PowerDNS: {$apppdns}\n";
echo "     - Yadifa: {$appyadifa}\n";
echo "   6. Mail: {$appqmail}\n";

if ($appdovecot !== '--uninstalled--') {
	echo "      - with: {$appdovecot}\n";
}

echo "      - pop3/imap4: {$pop3app}\n";
echo "      - smtp: {$smtpapp}\n";
echo "      - spam: {$spamapp}\n";

echo "   7. FTP: pure-ftpd\n";
echo "      - pure-ftpd: {$appftp}\n";

echo "   8. Stats: {$webstatsprog}\n";
echo "      - {$webstatsprog}: {$appstats}\n";

echo "D. Memory:\n";
foreach ($meminfo as $k => $v) {
	echo $v . "\n";
}

echo "E. Disk Space:\n";
foreach ($diskinfo as $k => $v) {
	echo "{$v}\n";
}

echo "\n";
