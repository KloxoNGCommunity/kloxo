<?php

rm_if_exists("/var/run/yum.pid");

// Make sure no issue with yum
system("yum-complete-transaction");

exec("sh /script/disable-firewall");

$lxlabspath = "/usr/local/lxlabs";
$kloxopath = "{$lxlabspath}/kloxo";
$currentpath = realpath(dirname(__FILE__));

// State must declare first
$kloxostate = getKloxoType();

$opt = parse_opt($argv);

$installtype = (isset($opt['install-type'])) ? $opt['install-type'] : 'master';
$installstep = (isset($opt['install-step'])) ? $opt['install-step'] : '1';

$mypass = password_gen();

$dbroot = "root";
// MR -- always set to ''
$dbpass = '';

// $osversion = find_os_version();


{ // Repos and Applications
    // If httpd24 already installed
    $yumWebIF = array(
        'httpd24u',
        'httpd24u-tools',
        'httpd24u-filesystem',
        'httpd24u-mod_security2',
        'mod24u_ssl',
        'mod24u_session',
        'mod24u_suphp',
        'mod24u_ruid2',
        'mod24u_fcgid',
        'mod24u_fastcgi',
        'mod24u_evasive'
    );

    $yumWeb = array(
        'httpd',
        'httpd-tools',
        'mod_rpaf',
        'mod_ssl',
        'mod_ruid2',
        'mod_fastcgi',
        'mod_fcgid',
        'mod_suphp',
        'mod_perl',
        'mod_define',
        'perl-Taint*'
    );


    $yumDNS = array('bind', 'bind-utils');


    // Old Mail packages to remove
    $yumMailRemove = array(
        'sendmail',
        'sendmail-cf',
        'sendmail-doc',
        'sendmail-devel',
        'vsftpd',
        'postfix',
        'ssmtp',
        'smail',
        'lxzend',
        'pure-ftpd',
        'exim'
    );

    $yumMail = array(
        'autorespond-toaster',
        'courier-authlib-toaster',
        'courier-imap-toaster',
        'daemontools-toaster',
        'ezmlm-toaster',
        'libdomainkeys-toaster',
        'libsrs2-toaster',
        'maildrop-toaster',
        'qmail-toaster',
        'ucspi-tcp-toaster',
        'vpopmail-toaster',
        'fetchmail',
        'bogofilter'
    );


    $yumOther = array(
        'pure-ftpd',
        'webalizer',
        'cronie',
        'cronie-anacron',
        'crontabs',
        'vixie-cron',
        'rpmdevtools',
        'yum-utils',
    );


    $yumRemoveOldLx = array('lxphp', 'lxlighttpd', 'lxzend', 'kloxo-*');

/*
    $yumInstallPackages = array(
        'tnef',
        'which',
        'gcc',
        'cpp',
        'gcc-c++',
        'zip',
        'unzip',
        'curl-devel',
        'libcurl-devel',
        'autoconf',
        'automake',
        'make',
        'libtool',
        'openssl-devel',
        'pure-ftpd',
        'yum-protectbase',
        'yum-plugin-replace',
        'crontabs',
        'make',
        'glibc-static',
        'net-snmp',
        'tmpwatch',
        'rkhunter',
        'quota',
        'xinetd',
        'screen',
        'telnet',
        'ncdu',
        'sysstat',
        'net-tools',
        'xz',
        'xz-libs',
        'p7zip',
        'p7zip-plugins',
        'rar',
        'unrar',
        'lxjailshell',
        'yum-presto',
        'deltarpm'
    );
*/
    $yumKloxoPackages = array(
        'kloxong-*.noarch',
        'kloxong-webmail-*.noarch',
        'kloxong-thirdparty-*.noarch',
        'kloxong-stats-*.noarch',
        'kloxong-editor-*.noarch',
        '--exclude=kloxong-thirdparty-phpmyadmin-*.noarch',
    );
}


/**
 * Main Function
 */
function lxins_main() 
{
    global $kloxopath, $kloxostate, $installtype, $installstep;

	// MR -- crucial because segfault if not exists
	if (!file_exists("{$kloxopath}/log")) {
		@mkdir("{$kloxopath}/log");
    }

//	$arch = trim( `arch` );
//	$arch = php_uname('m');

	// MR -- to make sure /tmp ready for all; found mysql not able to start if not 1777
	system("chmod 1777 /tmp");

	// MR -- modified sysctl.conf because using socket instead port for php-fpm
	$pattern = "fs.file-max";
	$sysctlconf = file_get_contents("/etc/sysctl.conf");

    print(">>> Modified /etc/sysctl.conf <<<\n");

	// MR - https://bbs.archlinux.org/viewtopic.php?pid=1002264
    // also add 'fs.aio-max-nr' for mysql 5.5 innodb aio issue
	$patch = "\n### begin -- add by Kloxo-MR\n" .
		"fs.aio-max-nr = 1048576\n" .
		"fs.file-max = 1048576\n" .
		"net.ipv4.tcp_syncookies = 1\n" .
		"net.ipv4.tcp_max_syn_backlog = 2048\n" .
		"net.ipv4.tcp_synack_retries = 3\n" .
		"#vm.swappiness = 10\n" .
		"#vm.vfs_cache_pressure = 100\n" .
		"#vm.dirty_background_ratio = 15\n" .
		"#vm.dirty_ratio = 5\n" .
		"### end -- add by Kloxo-MR\n";

    if (strpos($sysctlconf, $pattern) !== false) {
        //
    } else {
		// MR -- problem with for openvz
		@exec("grep envID /proc/self/status", $out, $ret);

        if ($ret === 0) {
            // no action
        } else {
            @system("echo '{$patch}' >> /etc/sysctl.conf; sysctl -e -p");
        }
    }

    if ($installstep === '2') {
        kloxo_install_step2();

        if ($installtype === 'master') {
			if (file_exists("/var/lib/mysql/kloxo")) {
                kloxo_install_bye();
            }
        }

        exit;
    }

    install_yum_repo();

    if (php_uname('m') === 'x86_64') {
        // Remove because make conflict
		@exec("yum remove *.i386 *.i686 -y >/dev/null 2>&1");
    }

    if ($kloxostate !== 'none') {
		//--- Create temporary flags for install
		@system("mkdir -p /var/cache/kloxo/");

        for ($x = 0; $x < 1000; $x++) {
            if (!file_exists("{$kloxopath}.old{$x}")) {
                system("'cp' -rf {$kloxopath} {$kloxopath}.old{$x}");
                break;
            }
        }
    }

    kloxo_install_step1();

    install_main();

    if ($kloxostate === 'none') {
        kloxo_install_step2();
    }

    kloxo_vpopmail();

    kloxo_prepare_kloxo_httpd_dir();

    kloxo_install_before_bye();

    if ($installtype === 'master') {
		if (file_exists("/var/lib/mysql/kloxo")) {
            kloxo_service_init();
            kloxo_install_bye();
        }
    } else {
        kloxo_service_init();
        kloxo_install_bye();
    }

}

/**
 * Fix Kloxo Service
 */
function kloxo_service_init()
{
    global $kloxopath;

	print(">>> Copy Kloxo-MR service <<<\n");

    exec("sh /script/fixlxphpexe");
}

// ==== kloxo_all portion ===

function install_general_mine($value)
{
	$value = implode(" ", $value);
    print("\nInstalling $value ....\n");
	system("yum -y install $value");
}

function install_main()
{
    install_web();
    install_php();
    install_database();
    install_dns();
    install_mail();
    install_others();
}

/**
 * Install web servers
 */
function install_web()
{
    global $kloxopath;

    global $yumWebIF, $yumWeb;

    $yumWif = implode(' ', $yumWebIF);
    $yumW   = implode(' ', $yumWeb);

    print(">>> Installing Apache and Hiawatha<<<\n");

	exec("yum list|grep ^httpd24u", $test);

    if (count($test) > 0) {
		system("yum remove -y httpd-* mod_*");
        system('yum install -y ' . $yumWif);
        if (!file_exists("{$kloxopath}/etc/flag")) {
            system("mkdir -p  {$kloxopath}/etc/flag");
        }

        system("echo '' > {$kloxopath}/etc/flag/use_apache24.flg");
    } else {
        system('yum install -y ' . $yumW);
    }

	system("yum install -y hiawatha");
}

/**
 * Install PHP
 */
function install_php()
{
    print(">>> Adding Standard PHP components<<<\n");
	// MR -- xcache, zend, ioncube, suhosin and zts not default install

    // For accept for php and apache branch rpm
    $phpbranch = getPhpBranch();

    system("sh /script/php-branch-installer {$phpbranch}");
}

/**
 * Install Database
 */
function install_database()
{
    print(">>> Installing Database services <<<\n");

    $mysql = getMysqlBranch();

    if (strpos($mysql, 'MariaDB') !== false) {
        // need separated because 'yum install MariaDB' will be install Galera
        //	system("yum -y install {$mysql}-server {$mysql}-shared");
        // already fix by MariaDB
        //	system("yum -y install {$mysql} {$mysql}-shared");
    } else {
        system("yum -y install {$mysql} {$mysql}-server {$mysql}-libs");
    }
}

/**
 * Install DNS tools
 */
function install_dns()
    global $yumDNS;
    $yumD = implode(' ', $yumDNS);

    print(">>> Installing DNS services <<<\n");

    system('yum -y install ' . $yumD);

	if (!file_exists("/var/log/named")) {
		@exec("mkdir -p /var/log/named; chown named:root /var/log/named; chmod 1777 /var/log/named");
	} else {
		@exec("chown named:root /var/log/named; chmod 1777 /var/log/named");
    }

	if (file_exists("/etc/rndc.conf")) {
        @exec("'rm' -f /etc/rndc.conf");
    }

//	@exec("sed -i 's/rndckey/rndc-key/' /etc/rndc.key");
}

/**
 * Install Mail Software
 */
function install_mail() {
    global $yumMailRemove, $yumMail;

    $yumMR = implode(' ', $yumMailRemove);
    $yumM  = implode(' ', $yumMail);

    print(">>> Removing $yumMR packages <<<\n");

	system("yum -y remove {$yumMR}");

    print(">>> Removing postfix user <<<\n");
    // force remove postfix and their user
	system("userdel postfix");

    // force remove spamassassin, qmail and vpopmail (because using toaster)
	system("userdel lxpopuser");
	system("groupdel lxpopgroup");

    print(">>> Installing Mail services <<<\n");

	system("yum -y install {$yumM}");

	system("groupadd -g 89 vchkpw");
    system("useradd -u 89 -g 89 vpopmail -s '/sbin/nologin'");
}

/**
 * Install Other Tools
 */
function install_others()
{
    global $yumOther;

    $yumO = implode(' ', $yumOther);

    print(">>> Installing OTHER services <<<\n");

	system("yum -y install {$yumO}");
}

/**
 * VPOP Mail database
 */
function kloxo_vpopmail()
{
    global $dbroot, $dbpass, $mypass;

    print(">>> Creating Vpopmail database <<<\n");

	if (file_exists("/home/vpopmail/etc")) {
        //	system("sh /usr/local/lxlabs/kloxo/bin/misc/vpop.sh $dbroot \"$dbpass\" vpopmail $mypass");
		system("sh /script/fixvpop");
    }

    print(">>> Fixing Vpopmail settings <<<\n");

	// file_put_contents("/etc/sysconfig/spamassassin", "SPAMDOPTIONS=\" -v -d -p 783 -u vpopmail\"");
	copy("/usr/local/lxlabs/kloxo/file/spamassassin/etc/sysconfig/spamassassin", "/etc/sysconfig/spamassassin");

    // until Kloxo-MR 6.5.1, still using the same mail path
	@system("mkdir -p /home/lxadmin/mail/domains");
	@system("chmod 755 /home/lxadmin");
	@system("chmod 755 /home/lxadmin/mail");
	@system("chmod 755 /home/lxadmin/mail/domains");

//	if (isRpmInstalled('qmail-toaster')) {
	@system("chmod 755 /home/vpopmail");
	@system("chmod 755 /home/vpopmail/domains");

	rm_if_exists("/etc/rc.d/init.d/courier-imap");
	rm_if_exists("/etc/rc.d/init.d/clamav");
	rm_if_exists("/etc/xinetd.d/smtp_lxa");
	rm_if_exists("/etc/xinetd.d/kloxo_smtp_lxa");
//	}

	@system("chmod -R 755 /var/log/httpd/");
	@system("mkdir -p /var/log/kloxo/");
	@system("mkdir -p /var/log/news");
}

/**
 * Installation step 1. It will create basic users and also install different packages
 */
function kloxo_install_step1()
{
    global $kloxopath, $kloxostate, $lxlabspath;


    global $yumRemoveOldLx, $yumInstallPackages, $yumKloxoPackages;

    $yumRemove   = implode(' ', $yumRemoveOldLx);
//    $yumPackages = implode(' ', $yumInstallPackages);
    $yumKloxoP   = implode(' ', $yumKloxoPackages);
    // disable this 'if' because trouble for update from lower version

    print(">>> Adding System users and groups (nouser, nogroup and lxlabs, lxlabs) <<<\n");
    @system("groupadd nogroup");
    @system("useradd nouser -g nogroup -s '/sbin/nologin'");
    @system("groupadd lxlabs");
    @system("useradd lxlabs -g lxlabs -s '/sbin/nologin'");

    print(">>> Removing DJBDns components <<<\n");
    if (!file_exists("/etc/rc.d/init.d/djbdns")) {
        $darr = array('axfrdns', 'dnscache', 'dnslog', 'tinydns');

        foreach ($darr as &$d) {
            rm_if_exists("/home/{$d}");
        }
    }

    // remove lxphp, lxlighttpd and lxzend
    print(">>> Removing 'old' lxphp/lxligttpd/lxzend/kloxo* <<<\n");
    system('yum remove -y ' . $yumRemove);
    if (file_exists("/usr/local/lxlabs/ext")) {
        rm_if_exists("/usr/local/lxlabs/ext");
    }

    print(">>> Adding certain components (like curl/contabs/rkhunter) <<<\n");
/*
    // Xcache, zend, ioncube, suhosin and zts not default install
    // install curl-devel (need by php-common) will be install curl-devel in CentOS 5 and libcurl-devel in CentOS 6


    system("yum -y install $yumPackages; rkhunter --update");
*/
	system("sh /script/rkhunter-installer");

    print(">>> Adding MalDetect <<<\n");

    system("sh /script/maldet-installer");

    print(">>> Adding Kloxo-NG webmail/thirparty/stats <<<\n");

    system("yum -y install " . $yumKloxoP);

    print(">>> Prepare installation directories <<<\n");

    system("mkdir -p {$kloxopath}");

    @chdir("/usr/local/lxlabs/kloxo");
    @system("mkdir -p {$kloxopath}/log");

    rm_if_exists("{$kloxopath}/kloxo-current.zip");
    rm_if_exists("{$kloxopath}/kloxo-mr-latest.zip");
    rm_if_exists("{$kloxopath}/kloxomr.tar.gz");
	rm_if_exists("{$kloxopath}/kloxo-ng-latest.zip");
    rm_if_exists("{$kloxopath}/kloxong.tar.gz");

    print(">>> Creating Symlink (in 64bit OS) for certain components <<<\n");

    if (php_uname('m') === 'x86_64') {
        if (file_exists("/usr/lib/php")) {
            @system("'mv' -f /usr/lib/php /usr/lib/php.bck");
        }

        $sls = array('php', 'httpd', 'lighttpd', 'nginx', 'mysql', 'perl');

        foreach ($sls as &$sl) {
            if (!file_exists("/usr/lib64/{$sl}")) {
                @system("mkdir -p /usr/lib64/{$sl}");
            }

            if (!is_link("/usr/lib/{$sl}")) {
                @system("ln -s /usr/lib64/{$sl} /usr/lib/{$sl}");
            }
        }
    }

    @system("chown -R lxlabs:lxlabs {$kloxopath}/cexe");
    @system("chmod -R 755 {$kloxopath}/cexe");
    @system("chmod -R ug+s {$kloxopath}/cexe");

    copy_script();

    @system("chown -R lxlabs:lxlabs {$lxlabspath}");

//	setUsingMyIsam();

    if (!isMysqlRunning()) {
        actionMySql('start');
    }
}

/**
 * Installation Step 2: Will set all the configurations and databases
 */
function kloxo_install_step2()
{
    global $kloxopath, $installtype;
    global $dbroot, $dbpass, $mypass;

    print(">>> Processing basic Kloxo-NG configures (setting and database) <<<\n");

    if (!file_exists("{$kloxopath}/etc/conf")) {
        system("mkdir -p {$kloxopath}/etc/conf");
    }

    if (!file_exists("{$kloxopath}/etc/conf/kloxo.pass")) {
        system("echo '{$mypass}' > ${kloxopath}/etc/conf/kloxo.pass");
    }

    if (!file_exists("{$kloxopath}/etc/slavedb")) {
        system("mkdir -p {$kloxopath}/etc/slavedb");
    }

    if (!file_exists("{$kloxopath}/etc/slavedb/dbadmin")) {
        if (strlen($dbpass) === 0) {
            $dbpassins = '';
        } else {
            $dbpassins = $dbpass;
        }

		$dbadmindata = 'O:6:"Remote":1:{s:4:"data";a:1:{s:5:"mysql";a:1:{s:10:"dbpassword";s:' .
			strlen($dbpass) . ':"' . $dbpassins . '";}}}';
        @system("echo '{$dbadmindata}' > {$kloxopath}/etc/slavedb/dbadmin");
    }

    if (!file_exists("{$kloxopath}/etc/slavedb/driver")) {
		$driverdata = 'O:6:"Remote":1:{s:4:"data";a:3:{s:3:"web";s:6:"apache";' .
			's:4:"spam";s:10:"bogofilter";s:3:"dns";s:4:"bind";' .
			's:4:"pop3";s:7:"courier";s:4:"smtp";s:5:"qmail";}}';
        //	system("echo '{$driverdata}' > {$kloxopath}/etc/slavedb/driver");
    }

    check_default_mysql();

    chdir("{$kloxopath}/httpdocs/");

	@system("lxphp.exe {$kloxopath}/bin/install/create.php " .
		"--install-type={$installtype} --db-rootuser={$dbroot} --db-rootpassword={$dbpass}");
}

function kloxo_install_easyinstaller()
{
    print(">>> Installing 'Easy Installer' <<<\n");
    @system("/script/easyinstaller-update"); // First run (gets easyinstallerdata)
    @system("/script/easyinstaller-update"); // Second run (gets applications)
}

function kloxo_prepare_kloxo_httpd_dir()
{
    print(">>> Preparing 'defaults' paths <<<\n");
    @system("mkdir -p /home/kloxo/httpd");
    rm_if_exists("/home/kloxo/httpd/skeleton-disable.zip");
    @system("chown -R apache:apache /home/kloxo/httpd");
}

function kloxo_install_before_bye()
{
    global $kloxopath;

    print(">>> Setup default configure for Webserver <<<\n");

    if (!isRpmInstalled('fetchmail')) {
        system("yum install fetchmail -y");
    }

    // because php 5.2 have problem with php-fpm
    if (version_compare(getPhpVersion(), "5.3.2", "<")) {
        $phpbranch = getPhpBranch();
        system("yum remove {$phpbranch}-fpm -y");
    }

    $sp = "{$kloxopath}/file/apache/etc/conf.d";
    $tp = "/etc/httpd/conf.d";

    // php-fpm_event as default instead mod_php
    if (file_exists("/etc/httpd/conf.d/php.conf")) {
		system("'cp' -rf {$sp}/fastcgi.conf {$tp}/fastcgi.conf;" .
			"'cp' -rf {$sp}/_inactive_.conf {$tp}/fcgid.conf;" .
			"'cp' -rf {$sp}/_inactive_.conf {$tp}/php.conf;" .
			"'cp' -rf {$sp}/_inactive_.conf {$tp}/ruid2.conf;" .
			"'cp' -rf {$sp}/_inactive_.conf {$tp}/suphp.conf;" .
			"'cp' -rf {$sp}/~lxcenter.conf {$tp}/~lxcenter.conf;" .
			"'cp' -rf {$sp}/ssl.conf {$tp}/ssl.conf;" .
			"'cp' -rf {$sp}/__version.conf {$tp}/__version.conf;" .
			"echo 'HTTPD=/usr/sbin/httpd.event' >/etc/sysconfig/httpd;");
    }

    //--- Prevent mysql socket problem (especially on 64bit system)
    if (!file_exists("/var/lib/mysql/mysql.sock")) {
        print("\nCreate mysql.sock...\n");
        actionMysql('stop');
        @system("mksock /var/lib/mysql/mysql.sock");
        actionMysql('start');
    }

    //--- Set ownership for Kloxo httpdocs dir
    system("chown -R lxlabs:lxlabs {$kloxopath}/httpdocs");

    if (!isMysqlRunning()) {
        //--- Prevent for Mysql not start after reboot for fresh kloxo slave install
        print("\nSetting Mysql for always running after reboot and restart now...\n");

        actionMysql('start');
    }
}

/**
 * Installation Completed
 */
function kloxo_install_bye()
{
    global $kloxostate, $installtype, $installstep;
//	$ip = gethostbyname(gethostname());
    $ip = gethostbyname(php_uname('n'));
    $l  = strlen($ip);

    $t = "\n";
    $t .= " _/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/ " . "\n";
    $t .= " _/                                                                          _/ " . "\n";
    $t .= " _/ Congratulations. Kloxo-MR has been installed succesfully as 'MASTER'     _/ " . "\n";
    $t .= " _/                                                                          _/ " . "\n";

    if ($installtype === 'master') {
        $t .= " _/ You can connect to the server at:                                        _/ " . "\n";
        $t .= " _/     https://{$ip}:7777 - secure ssl connection, or" . str_repeat(" ", 28 - $l) . "_/ " . "\n";
        $t .= " _/     http://{$ip}:7778 - normal one." . str_repeat(" ", 43 - $l) . "_/ " . "\n";
        $t .= " _/                                                                          _/ " . "\n";
        $t .= " _/ The login and password are 'admin' and 'admin' for new install.          _/ " . "\n";
        $t .= " _/ After Logging in, you will have to change your password to               _/ " . "\n";
        $t .= " _/ something more secure.                                                   _/ " . "\n";
        $t .= " _/                                                                          _/ " . "\n";
    } else {
        $t .= " _/ You should open the port 7779 on this server, since this is used for     _/ " . "\n";
        $t .= " _/ the communication between master and slave                               _/ " . "\n";
        $t .= " _/                                                                          _/ " . "\n";
        $t .= " _/ To access this slave, to go admin->servers->add server,                  _/ " . "\n";
        $t .= " _/ give the ip/machine name of this server. The password is 'admin'.        _/ " . "\n";
        $t .= " _/                                                                          _/ " . "\n";
        $t .= " _/ The slave will appear in the list of slaves, and you can access it       _/ " . "\n";
        $t .= " _/ just like you access localhost                                           _/ " . "\n";
        $t .= " _/                                                                          _/ " . "\n";
    }

    if ($kloxostate !== 'none') {
        $t .= " _/ - Need running 'sh /script/cleanup' for update                           _/ " . "\n";
    }

//	if ($installstep === '2') {
    //	$t .= " _/ - Better reboot for fresh install                                        _/ "."\n";
    $t .= " _/ - Run 'sh /script/mysql-convert --engine=myisam' to minimize MySQL       _/ " . "\n";
    $t .= " _/   memory usage. Or, go to 'Webserver Configure'                          _/ " . "\n";
    $t .= " _/ - Run 'sh /script/make-slave' for change to 'SLAVE'                      _/ " . "\n";
//	}

    if (isRpmInstalled('qmail')) {
        $t .= " _/ - Run 'sh /script/convert-to-qmailtoaster' to convert qmail-toaster      _/ " . "\n";
    }

    $t .= " _/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/ " . "\n";
    $t .= "\n";

    print($t);
}

// ==== kloxo_common portion ===

// this class must be exist for slave_get_db_pass()
class remote
{
}

/**
 * Parse options provided and return string
 * @param $argv
 * @return null
 */
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

        $opt          = explode("=", $v);
        $opt[0]       = substr($opt[0], 2);
        $ret[$opt[0]] = $opt[1];
    }

    return $ret;
}

/**
 * Generate a random password
 * @return string
 */
function password_gen()
{
    return randomString(10);
}

/**
 * Search character in string
 * @param $haystack
 * @param $needle
 * @return bool
 */
function char_search_beg($haystack, $needle)
{
	if (strpos($haystack, $needle) === 0) {
		return true;
	} else {
		return false;
	}
}

/**
 * Install yum repo
 */
function install_yum_repo()
{
    print(">>> Modified kloxong.repo and remove older repo names <<<\n");
    /*
        if (!file_exists("/etc/yum.repos.d")) {
            print("- No yum.repos.d dir detected!\n");

            return;
        }

        // just to know @ exist or not because centos 6 change 'installed' to '@'
        @exec("yum list *yum*|grep '@'", $out, $ret);

        // need for OS (like fedora) where os version not the same with redhat/centos
        if (count($out) > 0) {
            $exec("rpm --qf '%{name}\n' -qf /sbin/init", $out2);

            if ($out[0] === 'systemd') {
                $ver = '7';
            } else {
                $ver = '6';
            }
            system("sed -i 's/\$releasever/{$ver}/' /etc/yum.repos.d/kloxong.repo");
        } else {
            system("sed -i 's/\$releasever/5/' /etc/yum.repos.d/kloxong.repo");
        }

        // remove all old repos
        rm_if_exists("/etc/yum.repos.d/kloxo-mr.repo");
		rm_if_exists("/etc/yum.repos.d/kloxo-ng.repo");
        rm_if_exists("/etc/yum.repos.d/kloxo-custom.repo");
        rm_if_exists("/etc/yum.repos.d/kloxo.repo");
        rm_if_exists("/etc/yum.repos.d/lxcenter.repo");
    */
    @system("sh /script/fixrepo");

    @system("yum clean all");
}

/**
 * Get Which OS is installed
 * @return string
 */
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
            $oss   = $mapos[0];
        }

        return $ossup[$osver[0]] . "-" . $oss;
	} else {

    print("\nThis Operating System is currently *NOT* supported.\n");

    exit;
	}
}

function get_yes_no($question, $default = 'n')
{
	if ($default != 'y') {
        $default  = 'n';
        $question .= ' [y/N]: ';
    } else {
        $question .= ' [Y/n]: ';
    }

    $ret = 'n';

    for (; ;) {
        print $question;
        flush();
        $input = fgets(STDIN, 255);
        $input = trim($input);
        $input = strtolower($input);

        if ($input == 'y' || $input == 'yes' || ($default == 'y' && $input == '')) {
            $ret = 'y';
        } else if ($input == 'n' || $input == 'no' || ($default == 'n' && $input == '')) {
            $ret = 'n';
        }
    }

    return $ret;
}

function addLineIfNotExist($filename, $pattern, $comment)
{

    if (file_exists($filename)) {
        $cont = file_get_contents($filename);
    } else {
        $cont = '';
    }

    if (!preg_match("+$pattern+i", $cont)) {
        @file_put_contents($filename, "\n$comment \n\n", FILE_APPEND);
        @file_put_contents($filename, $pattern, FILE_APPEND);
        @file_put_contents($filename, "\n\n\n", FILE_APPEND);
    } else {
        print("\nPattern '$pattern' Already present in $filename\n");
    }
}

// taken from lib.php

 * Check which PHP RPM is installed
 * @return mixed|string
 */

$a = explode(",", file_get_contents('/usr/local/lxlabs/kloxo/etc/list/set.php.lst'));


    foreach ($a as &$e) {
        if (isRpmInstalled("{$e}-cli")) {
            return $e;
        }
    }

	return 'php56u';
}

/**
 * Check which DB is installed
 * @return mixed|string
 */
function getMysqlBranch()
{
    $a = array('mysql', 'mysql55', 'mysql56', 'mariadb', 'MariaDB');

    foreach ($a as &$e) {
        if (isRpmInstalled($e . '-server')) {
            return $e;
        }
    }

    return 'mysql';
}

/**
 * Get version of a package provided
 * @param $rpmname string RPM Name
 * @return string
 */
function getRpmVersion($rpmname)
{

    exec("rpm -q --qf '%{VERSION}\n' {$rpmname}", $out, $ret);

	if ($ret === 0) {
		$ver = $out[0];
	} else {
		$ver = '';
	}

    return $ver;
}

/**
 * Get PHP Version installed
 * @return string
 */
function getPhpVersion()
{
    exec("php -v|grep 'PHP'|grep '(built:'|awk '{print $2}'", $out, $ret);

    // 'php -v' may not work when php 5.4/5.5 using php.ini from 5.2/5.3
	if ($ret === 0) {
		return $out[0];
	} else {
		return '5.4.0';
	}
}

/**
 * Check if RPM Package is installed or not
 * @param $rpmname
 * @return bool
 */
function isRpmInstalled($rpmname)
{
    exec("rpm -qa {$rpmname}", $out);

	if (count($out) < 1) {
		return false;
	} else {
		return true;
	}
}

/**
 * Set Table engine to MyISAM
 *
 * taken from mysql-convert.php with modified
 * to make fresh install already use myisam as storage engine
 * with purpose minimize memory usage (save around 100MB)
 * @return bool
 */
function setUsingMyIsam()
{
    global $kloxostate;

    // taken from mysql-convert.php with modified
    // to make fresh install already use myisam as storage engine
    // with purpose minimize memory usage (save around 100MB)

    $mysqlver = getRpmVersion('mysql');

    if (version_compare($mysqlver, '5.5.0', ">=")) {
        // MySQL (also MariaDB) no permit 'skip-innodb'
        return false;
    }

    if ($kloxostate === 'none') {
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

        $string_source  = "[mysqld]\n";
        $string_replace = "[mysqld]\nskip-innodb\ndefault-storage-engine=myisam\n#performance_schema=on\n";

        $string_collect = str_replace($string_source, $string_replace, $string_collect);

        @file_put_contents($file, $string_collect);
    }

    return true;
}

/**
 * Check if MySQL is already running
 * @return bool
 */
function isMysqlRunning()
{
    @exec("pgrep ^mysql", $out);

	if (count($out) > 0) {
		return true;
	} else {
		return false;
	}
}

/**
 * Perform an action on Mysql, to Start, Stop or Restart MySQL server
 *
 * @param $action string Start, Stop or Restart
 */
function actionMysql($action)
{
    if ((file_exists("/etc/rc.d/init.d/mysqld")) || (file_exists("/usr/lib/systemd/system/mysqld.service"))) {
        system("service mysqld {$action}");
    } else {
        system("service mysql {$action}");
    }
}

/**
 *
 */
function copy_script()
{
    global $kloxopath;
    /*
        print(">>> Copying scripts to /scripts path <<<\n");

        system("mkdir -p /script/filter");

        system("'cp' -rf {$kloxopath}/pscript/* /script/");

        file_put_contents("/script/programname", 'kloxo');
        system("chmod 0775 /script");
    */
//	print(">>> Symlink '{$kloxopath}/pscript' to '/script' path <<<\n");
//	unlink("/script");
//	symlink("{$kloxopath}/pscript", "/script");

	// MR -- move to setup/installer.sh
//	@exec("'rm' -rf /script; ln -sf {$kloxopath}/pscript /script");
}

/**
 * Get Kloxo Type, if it is Master, Slave or None
 * @return string
 */
function getKloxoType()
{
    global $kloxopath;

    if (file_exists("{$kloxopath}/etc/conf/slave-db.db")) {
        return 'slave';
	} else {
		if (file_exists("/var/lib/mysql/kloxo")) {
        return 'master';
    } else {
        return 'none';
    }
	}
}

/**
 * Check Default MySQL Table
 */
function check_default_mysql()
{
    global $dbroot, $dbpass;

    if (!isMysqlRunning()) {
        actionMySql('start');
    }

    if ($dbpass !== '') {
        @exec("echo \"show tables\" | mysql -u {$dbroot} -p\"{$dbpass}\" mysql", $out, $ret);
    } else {
        @exec("echo \"show tables\" | mysql -u {$dbroot} mysql", $out, $ret);
    }

    if ($ret !== 0) {
        resetDBPassword();
    }
}

/**
 * Reset DB Password
 */
function resetDBPassword()
{
    global $dbpass;

    @system("sh /script/reset-mysql-root-password {$dbpass}");
}

/**
 * Generate a random string
 * @param $length int length of string required
 * @return string
 */
function randomString($length)
{
    $key = '';

    $keys = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));

    for ($i = 0; $i < $length; $i++) {
        $key .= $keys[array_rand($keys)];
    }

    return $key;
}

/**
 * Run a command
 * @param $input
 */
function exec_out($input)
{
	if (!$input) { return; }

    @exec($input, $out);

    if ($out) {
        print("\n" . implode("\n", $out) . "\n");
    }

    $out = null;
}

/**
 * Delete a file from server if it is found
 * @param $file
 */
function rm_if_exists($file)
{
    if (file_exists($file)) {
        @system("'rm' -rf {$file}");
    }
}

lxins_main();
