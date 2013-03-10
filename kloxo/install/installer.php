<?php
// Kloxo, Hosting Control Panel
//
// Copyright (C) 2000-2009	LxLabs
// Copyright (C) 2009-2011	LxCenter
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Affero General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Affero General Public License for more details.
//
// You should have received a copy of the GNU Affero General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//

// ==== kloxo_installer portion ===

$lxlabspath = "/usr/local/lxlabs";
$kloxopath = "{$lxlabspath}/kloxo";
$currentpath = realpath(dirname(__FILE__));

date_default_timezone_set('UTC');
$stamp = date("Y-m-d-H-i-s");

function lxins_main()
{
	global $argv;
	global $lxlabspath, $kloxopath, $currentpath, $stamp;

	// MR -- for to make sure
	exec("yum clean all");

	$opt = parse_opt($argv);
	$dir_name = dirname(__FILE__);
	$installtype = $opt['install-type'];
	$dbroot = "root";
	$dbpass = (slave_get_db_pass()) ? slave_get_db_pass() : "";
	$osversion = find_os_version();
	// $arch = trim( `arch` );
 	// $arch = php_uname('m');

	$licenseagree = (isset($opt['license-agree'])) ? $opt['license-agree'] : null;
	$noasking = (isset($opt['no-asking'])) ? $opt['no-asking'] : null;

	if (!char_search_beg($osversion, "centos") && !char_search_beg($osversion, "rhel")) {
		print("Kloxo is only supported on CentOS 5 and RHEL 5\n");

		exit;
	}

	print("Installing LxCenter yum repository for updates\n");
	install_yum_repo($osversion);

	$mypass = password_gen();

	// MR -- also issue on Centos 5.9 - prevent for update!
	if (php_uname('m') === 'x86_64') {
		system("yum remove mysql*.i386 mysql*.i686 -y");
	}
		
	if (getKloxoType() !== '') {
		//--- Create temporary flags for install
		system("mkdir -p /var/cache/kloxo/");

		if ($noasking !== 'yes') {
			//--- Ask Reinstall
			if (get_yes_no("\nKloxo seems already installed do you wish to continue?") == 'n') {
				print("Installation Aborted.\n");

				exit;
			}
		}

		exec("cp -rf {$kloxopath} {$kloxopath}.{$stamp}");
		exec("rm -rf {$kloxopath}/file/*");
		exec("rm -rf {$kloxopath}/pscript/*");
		exec("rm -rf {$kloxopath}/httpdocs/htmllib/script/*");

	} else {
		// MR -- issue found on Centos 5.9 where have 'default' iptables config
		$iptp = '/etc/sysconfig';
		$ipts = array('iptables', 'ip6tables');

		foreach ($ipts as &$ipt) {
			if (file_exists("{$iptp}/{$ipt}")) {
				system("mv -f {$iptp}/{$ipt} {$iptp}/{$ipt}.kloxosave");
			}
		}
		
		if (($noasking !== 'yes') || ($licenseagree !== 'yes')) {
			print("\n*** You are installing Kloxo-MR (Kloxo fork by Mustafa Ramadhan ***\n");
			print("- Better using backup-restore process for update from Kloxo 6.1.12+.\n");
			print("  No guarantee always success update from Kloxo after 6.1.12 version\n\n");


			//--- Ask License
			if (get_yes_no("Kloxo is using AGPL-V3.0 License, do you agree with the terms?") == 'n') {
				print("You did not agree to the AGPL-V3.0 license terms.\n");
				print("Installation aborted.\n\n");
				exit;
			} else {
				print("Installing Kloxo-MR = YES\n\n");
			}
		}
	}

	// MR -- disable asking for installing installapp where installapp not installed now
	/*
		//--- Ask for InstallApp
		print("InstallApp: PHP Applications like PHPBB, WordPress, Joomla etc\n");
		print("When you choose Yes, be aware of downloading about 350Mb of data!\n");

		if (get_yes_no("Do you want to install the InstallAPP sotfware?") == 'n') {
			print("Installing InstallApp = NO\n");
			print("You can install it later with /script/installapp-update\n\n");
			$installappinst = false;
		} else {
			print("Installing InstallApp = YES\n\n");
			$installappinst = true;
		}
	*/

	kloxo_install_step1();

	if ($installtype !== 'slave') {
		check_default_mysql($dbroot, $dbpass);
	}

	print("Prepare defaults and configurations...\n");
	install_main();

	kloxo_vpopmail($dir_name, $dbroot, $dbpass, $mypass);

	kloxo_prepare_kloxo_httpd_dir();

	if (getKloxoType() === '') {
		kloxo_install_step2($installtype, $dbroot, $dbpass);
	}
	
/*
	if ($installappinst) {
		kloxo_install_installapp();
	}
*/
	
	kloxo_install_before_bye();

	system("/etc/init.d/kloxo restart >/dev/null 2>&1 &");

	kloxo_install_bye($installtype);
}

// ==== kloxo_all portion ===

function install_general_mine($value)
{
	$value = implode(" ", $value);
	print("Installing $value ....\n");
	system("PATH=\$PATH:/usr/sbin yum -y install $value");
}

function installcomp_mail()
{
	system('pear channel-update "pear.php.net"'); // to remove old channel warning
	system("pear upgrade --force pear"); // force is needed
	system("pear upgrade --force Archive_Tar"); // force is needed
	system("pear upgrade --force structures_graph"); // force is needed
	system("pear install log");
}

function install_main()
{
	// MR -- need outside process for convert qmail-lxcenter to qmail-toaster
	if (isRpmInstalled('qmail')) {
		$installcomp['mail'] = array("httpd", "fetchmail");
	} else {
		$installcomp['mail'] = array("httpd", "autorespond-toaster", "courier-authlib-toaster",
			"courier-imap-toaster", "daemontools-toaster", "ezmlm-toaster", "libdomainkeys-toaster",
			"libsrs2-toaster", "maildrop-toaster", "qmail-pop3d-toaster", "qmail-toaster",
			"ripmime-toaster", "ucspi-tcp-toaster", "vpopmail-toaster", "fetchmail", "bogofilter");
	}

	$installcomp['web'] = array("httpd", "pure-ftpd");
	$installcomp['dns'] = array("bind", "bind-chroot");
	$installcomp['database'] = array(getMysqlBranch());

	// global $argv;
	$comp = array("web", "mail", "dns", "database");

	$serverlist = $comp;

	foreach ($comp as $c) {
		flush();

		if (array_search($c, $serverlist) !== false) {
			print("Installing $c Components....");
			$req = $installcomp[$c];
			$func = "installcomp_$c";

			if (function_exists($func)) {
				$func();
			}

			install_general_mine($req);
			print("\n");
		}
	}

	$options_file = "/var/named/chroot/etc/global.options.named.conf";

	$example_options = "acl \"lxcenter\" {\n";
	$example_options .= "\tlocalhost;\n";
	$example_options .= "};\n\n";
	$example_options .= "options {\n";
	$example_options .= "\tmax-transfer-time-in 60;\n";
	$example_options .= "\ttransfer-format many-answers;\n";
	$example_options .= "\ttransfers-in 60;\n";
	$example_options .= "\tauth-nxdomain yes;\n";
	$example_options .= "\tallow-transfer { \"lxcenter\"; };\n";
	$example_options .= "\tallow-recursion { \"lxcenter\"; };\n";
	$example_options .= "\trecursion no;\n";
	$example_options .= "\tversion \"LxCenter-1.0\";\n";
	$example_options .= "};\n\n";
	$example_options .= "# Remove # to see all DNS queries\n";
	$example_options .= "# logging {\n";
	$example_options .= "#\t channel query_logging {\n";
	$example_options .= "#\t\t file \"/var/log/named_query.log\";\n";
	$example_options .= "#\t\t versions 3 size 100M;\n";
	$example_options .= "#\t\t print-time yes;\n";
	$example_options .= "#\t };\n\n";
	$example_options .= "#\t category queries {\n";
	$example_options .= "#\t\t query_logging;\n";
	$example_options .= "#\t };\n";
	$example_options .= "# };\n";

	if (!file_exists($options_file)) {
		touch($options_file);
		chown($options_file, "named");
	}

	$cont = file_get_contents($options_file);
	$pattern = "options";

	if (!preg_match("+$pattern+i", $cont)) {
		file_put_contents($options_file, "$example_options\n");
	}

	$pattern = 'include "/etc/kloxo.named.conf";';
	$file = "/var/named/chroot/etc/named.conf";
	$comment = "//Kloxo";
	
	if (!file_exists($file)) {
		addLineIfNotExist($file, $pattern, $comment);
		touch($file);
		chown($file, "named");
	}
}

function kloxo_vpopmail($dir_name, $dbroot, $dbpass, $mypass)
{
	file_put_contents("/etc/sysconfig/spamassassin", "SPAMDOPTIONS=\" -v -d -p 783 -u vpopmail\"");

	print("\nCreating Vpopmail database...\n");

	if (file_exists("/home/vpopmail/etc")) {
		system("sh /usr/local/lxlabs/kloxo/bin/misc/vpop.sh $dbroot \"$dbpass\" vpopmail $mypass");
	}
	
	if (file_exists("/home/lxadmin/mail/etc")) {
		system("sh /usr/local/lxlabs/kloxo/bin/misc/lxpop.sh $dbroot \"$dbpass\" vpopmail $mypass");
	}

	// MR -- until Kloxo-MR 6.5.1, still using the same mail path
	system("mkdir -p /home/lxadmin/mail/domains");
	system("chmod 755 /home/lxadmin");
	system("chmod 755 /home/lxadmin/mail");
	system("chmod 755 /home/lxadmin/mail/domains");

	if (isRpmInstalled('qmail-toaster')) {
		system("chmod 755 /home/vpopmail");
		system("chmod 755 /home/vpopmail/domains");

		system("rm -f /etc/rc.d/init.d/courier-imap");
		system("rm -f /etc/rc.d/init.d/clamav");
		system("rm -f /etc/xinetd.d/smtp_lxa");
		system("rm -f /etc/xinetd.d/kloxo_smtp_lxa");
	}

	system("chmod -R 755 /var/log/httpd/");
	system("chmod -R 755 /var/log/httpd/fpcgisock >/dev/null 2>&1");
	system("mkdir -p /var/log/kloxo/");
	system("mkdir -p /var/log/news");
}

function kloxo_install_step1()
{
	global $argv;
	global $lxlabspath, $kloxopath, $currentpath, $stamp;

//	if (getKloxoType() === '') {
	print("Adding System users and groups (nouser, nogroup and lxlabs, lxlabs)\n");
	system("groupadd nogroup");
	system("useradd nouser -g nogroup -s '/sbin/nologin'");
	system("groupadd lxlabs");
	system("useradd lxlabs -g lxlabs -s '/sbin/nologin'");

	// MR -- remove qmail-lxcenter not here! - need outside script
	$packages = array("sendmail", "sendmail-cf", "sendmail-doc", "sendmail-devel",
		"exim", "vsftpd", "postfix", "ssmtp",
		"lxzend", "pure-ftpd");

	$list = implode(" ", $packages);
	print("Removing packages $list...\n");

	foreach ($packages as $package) {
		system("rpm -e --nodeps $package > /dev/null 2>&1");
	}

	// MR -- force remove old lxphp (from lxcenter.repo)
	system("rpm -e lxphp-5.2.1-400.i386 --nodeps > /dev/null 2>&1");

	if (isRpmInstalled('qmail-toaster')) {
		// MR -- force remove spamassassin, qmail and vpopmail (because using toaster)
		system("userdel lxpopuser > /dev/null 2>&1");
		system("groupdel lxpopgroup > /dev/null 2>&1");
		
		system("groupadd -g 89 vchkpw > /dev/null 2>&1");
		system("useradd -u 89 -G vchkpw vpopmail -s '/sbin/nologin' > /dev/null 2>&1");
	}
	
	if (!file_exists("/etc/rc.d/init.d/djbdns")) {
		$darr = array ('axfrdns', 'dnscache', 'dnslog', 'tinydns');
		
		foreach ($darr as &$d) {
			system("rm -rf /home/{$d} > /dev/null 2>&1");
		}
	}

	// MR -- force remove postfix and their user
	system("userdel postfix > /dev/null 2>&1");

	// MR -- for accept for php and apache branch rpm
	$phpbranch = getPhpBranch();
	$httpdbranch = getApacheBranch();
	$mysqlbranch = getMysqlBranch();

	// MR -- xcache, zend, ioncube, suhosin and zts not default install
	// php from atomic may problem when install php-mysql without together with php-pdo (install php 5.2 on centos 6.x)
	$packages = array("{$phpbranch}-mbstring", "{$phpbranch}-mysql", "{$phpbranch}-pdo", "which", "gcc-c++",
		"{$phpbranch}-imap", "{$phpbranch}-pear", "{$phpbranch}-gd", "{$phpbranch}-devel", "{$phpbranch}-pspell",
		"tnef", "lxlighttpd", $httpdbranch, "mod_ssl",
		"zip", "unzip", "lxphp", "{$mysqlbranch}", "{$mysqlbranch}-server", "curl", "autoconf", "automake", "mod_ruid2",
		"libtool", "gcc", "cpp", "openssl", "pure-ftpd", "yum-protectbase", "yum-plugin-replace", "crontabs",
		"kloxomr-webmail-*.noarch", "kloxomr-addon-*.noarch", "kloxomr-thirdparty-*.noarch", "net-snmp", "tmpwatch", "rkhunter",
		"quota"
	);

	$list = implode(" ", $packages);

	while (true) {
		print("Installing packages $list...\n");
		system("PATH=\$PATH:/usr/sbin yum -y install $list", $return_value);

		if (file_exists("{$lxlabspath}/ext/php/php")) {
			break;
		} else {
			print("YUM Gave Error... Trying Again...\n");
			if (get_yes_no("Try again?") == 'n') {
				print("- EXIT: Fix the problem and install Kloxo again.\n");
				exit;
			}
		}
	}
//	}

	print("Prepare installation directory\n");

	system("mkdir -p {$kloxopath}");

	if (file_exists("../../kloxo-mr-latest.zip")) {
		//--- Install from local file if exists
		system("rm -f {$kloxopath}/kloxo-current.zip");
		system("rm -f {$kloxopath}/kloxo-mr-latest.zip");

		print("Local copying Kloxo release\n");
		system("mkdir -p /var/cache/kloxo");
		system("cp -rf ../../kloxo-mr-latest.zip {$kloxopath}");

		chdir("/usr/local/lxlabs/kloxo");
		system("mkdir -p {$kloxopath}/log");
	} else {
		chdir("/usr/local/lxlabs/kloxo");
		system("mkdir -p {$kloxopath}/log");

		system("rm -f {$kloxopath}/kloxo-current.zip");
		system("rm -f {$kloxopath}/kloxo-mr-latest.zip");
	}

	if (php_uname('m') === 'x86_64') {
		if (file_exists("/usr/lib/php")) {
			system("mv -f /usr/lib/php /usr/lib/php.bck");
		}

		system("mkdir -p /usr/lib64/php");
		system("ln -s /usr/lib64/php /usr/lib/php");
		system("mkdir -p /usr/lib64/httpd");
		system("ln -s /usr/lib64/httpd /usr/lib/httpd");
		system("mkdir -p /usr/lib64/lighttpd");
		system("ln -s /usr/lib64/lighttpd /usr/lib/lighttpd");
		system("mkdir -p /usr/lib64/mysql");
		system("ln -s /usr/lib64/mysql /usr/lib/mysql");
	}

	print("\n\nInstalling Kloxo.....\n\n");

	system("unzip -oq kloxo-mr-latest.zip -d ../");

	system("chmod -R 755 {$kloxopath}/cexe");

	copy_script();

	system("rm -f {$kloxopath}/kloxo-mr-latest.zip");

	system("chown -R lxlabs:lxlabs {$lxlabspath}");
	chdir("{$kloxopath}/httpdocs/");

	setUsingMyIsam();

	if (!isMysqlRunning()) {
		system("service mysqld start");
	}
}

function kloxo_install_step2($installtype, $dbroot, $dbpass)
{
	global $argv;
	global $lxlabspath, $kloxopath, $currentpath, $stamp;

	chdir("{$kloxopath}/httpdocs/");
	system("{$lxlabspath}/ext/php/php {$kloxopath}/bin/install/create.php " .
		"--install-type=$installtype --db-rootuser=$dbroot --db-rootpassword=$dbpass");
}

function kloxo_install_installapp()
{
	print("Install InstallApp...\n");
	system("/script/installapp-update"); // First run (gets installappdata)
	system("/script/installapp-update"); // Second run (gets applications)
}

function kloxo_prepare_kloxo_httpd_dir()
{
	print("Prepare /home/kloxo/httpd...\n");
	system("mkdir -p /home/kloxo/httpd");

	system("rm -rf /home/kloxo/httpd/skeleton-disable.zip");

	system("chown -R lxlabs:lxlabs /home/kloxo/httpd");
}

function kloxo_install_before_bye()
{
	global $argv;
	global $lxlabspath, $kloxopath, $currentpath, $stamp;

	system("cp -rf {$currentpath}/kloxo-mr.repo {$kloxopath}/file");

	// MR -- because php 5.2 have problem with php-fpm
	if (version_compare(getPhpVersion(), "5.3.2", "<")) {
		$phpbranch = getPhpBranch();
		system("yum remove {$phpbranch}-fpm -y");
	}

	// MR -- ruid2 as default instead mod_php
	if (file_exists("/etc/httpd/conf.d/php.conf")) {
		system("mv -f /etc/httpd/conf.d/php.conf /etc/httpd/conf.d/php.nonconf");
		// MR -- because /home/apache no exist at this step
		system("cp -rf {$kloxopath}/file/apache/etc/conf.d/ruid2.conf /etc/httpd/conf.d/ruid2.conf");
	}

	/*
		if (getKloxoType() === 'master') {
			$reinst = true;
		} else {
			$reinst = false;
		}
	*/

	//--- Prevent mysql socket problem (especially on 64bit system)
	if (!file_exists("/var/lib/mysql/mysql.sock")) {
		print("Create mysql.sock...\n");
		actionMysql("stop");
		system("mksock /var/lib/mysql/mysql.sock");
		actionMysql('start');
	}

	//--- Set ownership for Kloxo httpdocs dir
	system("chown -R lxlabs:lxlabs {$kloxopath}/httpdocs");

	if (!isMysqlRunning()) {
		//--- Prevent for Mysql not start after reboot for fresh kloxo slave install
		print("Setting Mysql for always running after reboot and restart now...\n");

		actionMysql('start');
	}
}

function kloxo_install_bye($installtype)
{
	print("\nCongratulations. Kloxo-MR has been installed succesfully as $installtype\n\n");
	if ($installtype === 'master') {
		print("You can connect to the server at:\n");
		print("	https://<ip-address>:7777 - secure ssl connection, or\n");
		print("	http://<ip-address>:7778 - normal one.\n\n");
		print("The login and password are 'admin' 'admin' for new install.\n");
		print("After Logging in, you will have to change your password to \n");
		print("something more secure\n\n");
	} else {
		print("You should open the port 7779 on this server, since this is used for\n");
		print("the communication between master and slave\n\n");
		print("To access this slave, to go admin->servers->add server,\n");
		print("give the ip/machine name of this server. The password is 'admin'.\n\n");
		print("The slave will appear in the list of slaves, and you can access it\n");
		print("just like you access localhost\n\n");
	}

	print("\n");
	print("---------------------------------------------\n");
	print("\n");

	if (getKloxoType() !== '') {
		print("- Need running 'sh /script/cleanup' for update\n\n");
	}
	
	print("- Better reboot for fresh install\n\n");

	if (isRpmInstalled('qmail')) {
		print("---------------------------------------------\n");
		print("\n");
		print("- Because still using qmail from lxcenter,\n");
		print("  run 'sh /script/convert-to-qmailtoaster'\n\n");
	}
}

// ==== kloxo_common portion ===

// MR -- this class must be exist for slave_get_db_pass()
class remote
{
}

function slave_get_db_pass()
{
	global $argv;
	global $lxlabspath, $kloxopath, $currentpath, $stamp;

	$rmt = file_get_unserialize("{$kloxopath}/etc/slavedb/dbadmin");

	if ($rmt) {
		return $rmt->data['mysql']['dbpassword'];
	} else {
		return false;
	}
}

function file_get_unserialize($file)
{
	if (!file_exists($file)) {
		return null;
	}

	return unserialize(file_get_contents($file));
}

function check_default_mysql($dbroot, $dbpass)
{
	if (!isMysqlRunning()) {
		actionMysql('restart');
	}

	if ($dbpass) {
		exec("echo \"show tables\" | mysql -u $dbroot -p\"$dbpass\" mysql", $out, $ret);
	} else {
		exec("echo \"show tables\" | mysql -u $dbroot mysql", $out, $ret);
	}

	if ($ret) {
		resetDBPassword($dbroot, $dbpass);
	}
}

function parse_opt($argv)
{
	unset($argv[0]);

	if (!$argv) {
		return null;
	}

	$ret = null;

	foreach ($argv as $v) {
		if (strstr($v, "=") === false || strstr($v, "--") === false) {
			continue;
		}

		$opt = explode("=", $v);
		$opt[0] = substr($opt[0], 2);
		$ret[$opt[0]] = $opt[1];
	}

	return $ret;
}

function password_gen()
{
	$data = mt_rand(2, 30);
	$pass = "lx" . $data; // lx is a indentifier

	return $pass;
}

function char_search_beg($haystack, $needle)
{
	if (strpos($haystack, $needle) === 0) {
		return true;
	} else {
		return false;
	}
}

function install_yum_repo()
{

	// global $dirpath;

	if (!file_exists("/etc/yum.repos.d")) {
		print("No yum.repos.d dir detected!\n");

		return;
	}

	system("cp -rf ./kloxo-mr.repo /etc/yum.repos.d/kloxo-mr.repo");

	// MR -- remove all old repos
	system("rm -f /etc/yum.repos.d/kloxo.repo");
	system("rm -f /etc/yum.repos.d/kloxo-custom.repo");
	system("rm -f /etc/yum.repos.d/lxcenter.repo");
	
	system("yum clean all");
}

function find_os_version()
{
	// list os support
	$ossup = array('redhat' => 'rhel', 'fedora' => 'fedora', 'centos' => 'centos');

	$osrel = null;

	foreach (array_keys($ossup) as $k) {
		$osrel = file_get_contents("/etc/{$k}-release");
		if ($osrel) {
			$osrel = strtolower(trim($osrel));
			break;
		}
	}

	// specific for 'red hat'
	$osrel = str_replace('red hat', 'redhat', $osrel);

	$osver = explode(" ", $osrel);

	$verpos = sizeof($osver) - 2;

	if (array_key_exists($osver[0], $ossup)) {
		// specific for 'red hat'
		if ($osrel === 'redhat') {
			$oss = $osver[$verpos];
		} else {
			$mapos = explode(".", $osver[$verpos]);
			$oss = $mapos[0];
		}

		return $ossup[$osver[0]] . "-" . $oss;
	} else {
		print("This Operating System is currently *NOT* supported.\n");

		exit;
	}
}

function get_yes_no($question, $default = 'n')
{
	if ($default != 'y') {
		$default = 'n';
		$question .= ' [y/N]: ';
	} else {
		$question .= ' [Y/n]: ';
	}
	for (; ;) {
		print $question;
		flush();
		$input = fgets(STDIN, 255);
		$input = trim($input);
		$input = strtolower($input);

		if ($input == 'y' || $input == 'yes' || ($default == 'y' && $input == '')) {
			return 'y';
		} else if ($input == 'n' || $input == 'no' || ($default == 'n' && $input == '')) {
			return 'n';
		}
	}
}

// --- taken from reset-mysql-root-password.phps
function resetDBPassword($user, $pass)
{
	$text = "UPDATE mysql.user SET Password=PASSWORD('PASSWORD') WHERE User='USER';" .
			"FLUSH PRIVILEGES;";
	$text = str_replace("'USER'", "'root'", $text);
	$text = str_replace("'PASSWORD'", "'{$pass}'", $text);

	file_put_contents("/tmp/reset-mysql-password.sql", $text);

	actionMysql('stop');

	sleep(5);

	print("Reset password in progress...\n");
	system("mysqld_safe --init-file=/tmp/reset-mysql-password.sql >/dev/null 2>&1 &");
	system("rm -f /tmp/reset-mysql-password.sql");

	sleep(5);

	actionMysql('start');

	print("Password successfully reset to \"$pass\"\n");
}

function addLineIfNotExist($filename, $pattern, $comment)
{

	if (file_exists($filename)) {
		$cont = file_get_contents($filename);
	} else {
		$cont = '';
	}

	if (!preg_match("+$pattern+i", $cont)) {
		file_put_contents($filename, "\n$comment \n\n", FILE_APPEND);
		file_put_contents($filename, $pattern, FILE_APPEND);
		file_put_contents($filename, "\n\n\n", FILE_APPEND);
	} else {
		print("Pattern '$pattern' Already present in $filename\n");
	}
}

// MR -- taken from lib.php
function getPhpBranch()
{
	$a = array('php', 'php52', 'php53', 'php53u', 'php54');

	foreach ($a as &$e) {
		if (isRpmInstalled($e)) {
			return $e;
		}
	}

	return 'php';
}

// MR -- taken from lib.php
function getApacheBranch()
{
	$a = array('httpd', 'httpd24');

	foreach ($a as &$e) {
		if (isRpmInstalled($e)) {
			return $e;
		}
	}

	return 'httpd';
}

// MR -- taken from lib.php
function getMysqlBranch()
{
	$a = array('mysql', 'mysql50', 'mysql51', 'mysql53', 'mysql55', 'MariaDB');

	foreach ($a as &$e) {
		if (isRpmInstalled($e . '-server')) {
			return $e;
		}
	}

	return 'mysql';
}

// MR -- taken from lib.php
function getRpmVersion($rpmname)
{
	exec("rpm -q {$rpmname}", $out, $ret);

	return str_replace($rpmname . '-', '', $out[0]);

}

// MR -- taken from lib.php
function getPhpVersion()
{
	exec("php -r 'echo phpversion();'", $out, $ret);

	return $out[0];
}

// MR -- taken from lib.php
function isRpmInstalled($rpmname)
{
	exec("rpm -q {$rpmname}", $out);

	$ret = strpos($out[0], "{$rpmname}-");

	// MR -- must be '!== 0' because no exist sometimes with value > 0; 0 because position in 0
	if ($ret !== 0) {
		return false;
	} else {
		return true;
	}
}

function setUsingMyIsam()
{
	// MR -- taken from mysql-convert.php with modified
	// to make fresh install already use myisam as storage engine
	// with purpose minimize memory usage (save around 100MB)
	
	$mysqlver = getRpmVersion('mysql');
	
	if (version_compare($mysqlver, '5.5.0', ">=")) {
		// MR MySQL (also MariaDB) no permit 'skip-innodb'
		return false;
	}

	if (getKloxoType() === '') {
		$file = "/etc/my.cnf";

		$string = file_get_contents($file);

		$string_array = explode("\n", $string);

		$string_collect = null;

		foreach ($string_array as $sa) {
			if (stristr($sa, 'skip-innodb') !== FALSE) {
				$string_collect .= "";
				continue;
			}

			if (stristr($sa, 'default-storage-engine') !== FALSE) {
				$string_collect .= "";
				continue;
			}
			$string_collect .= $sa . "\n";
		}

		$string_source = "[mysqld]\n";
		$string_replace = "[mysqld]\nskip-innodb\ndefault-storage-engine=myisam\n";

		$string_collect = str_replace($string_source, $string_replace, $string_collect);

		file_put_contents($file, $string_collect);
	}
}

function isMysqlRunning()
{
	if (file_exists("/etc/rc.d/init.d/mysql")) {
		exec("service mysql status|grep -i 'running'", $out, $ret);
	} else {
		exec("service mysqld status|grep -i 'running'", $out, $ret);
	}

	if ($out) {
		return true;
	} else {
		return false;
	}
}

function actionMysql($action)
{
	if (file_exists("/etc/rc.d/init.d/mysql")) {
		exec("service mysql {$action}");
	} else {
		exec("service mysqld {$action}");
	}
}

function copy_script()
{
	global $argv;
	global $lxlabspath, $kloxopath, $currentpath, $stamp;

	exec("mkdir -p /script/filter");

	exec("cp -rf {$kloxopath}/httpdocs/htmllib/script/* /script/");
	exec("cp -rf {$kloxopath}/pscript/* /script/");

	file_put_contents("/script/programname", 'kloxo');
	exec("chmod 0775 /script");
}

function getKloxoType()
{
	global $argv;
	global $lxlabspath, $kloxopath, $currentpath, $stamp;

	if (file_exists("/var/lib/mysql/kloxo")) {
		return 'master';
	} else if (file_exists("{$kloxopath}/etc/conf/slave-db.db")) {
		return 'slave';
	} else {
		return '';
	}
}

lxins_main();
