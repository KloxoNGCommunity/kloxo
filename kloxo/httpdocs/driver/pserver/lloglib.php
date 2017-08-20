<?php

class Llog extends Lxclass {

	static $__desc = array("", "",  "log_manager");

	// Data
	static $__desc_nname = array("", "",  "server_name", "a=show");
	static $__acdesc_show = array("", "",  "log_manager", "a=show");


	static $__desc_ffile_l = array('v', '', '', '');

	function get() {}
	function write() {}

	static function initThisObjectRule($parent, $class, $name = null)
	{
		return $parent->getClName();
	}

	function getId()
	{
		return $this->getSpecialname();
	}

	function createShowPropertyList(&$alist)
	{
		$alist['property'][] = 'a=show';
		$alist['property'][] = 'a=show&l[class]=ffile&l[nname]=/';
	}

	function createShowAlist(&$alist, $subaction = null)
	{
		return $alist;
	}

	function getFfileFromVirtualList($name)
	{
		global $sgbl;

		if (substr($name, 0, 1) !== '/') {
		//	$name = coreFfile::getRealpath($name);
			$name = '/var/log/' . $name;
		}

		$ffile= new Ffile($this->__masterserver, $this->__readserver, "/", $name, $this->getParentO()->username);

		$ffile->__parent_o = $this;
		$ffile->get();
		$ffile->readonly = 'on';

		return $ffile;
	}


	function createShowSclist()
	{
		$phplist = getMultiplePhpList();

		$phploglist = array('php-error.log' => 'php error');
		$phpfpmloglist = array('php-fpm/php-error.log' => 'php-fpm error');

		foreach ($phplist as $k => $v) {
			$phploglist["{$v}-error.log"] = "{$v} error";
			$phpfpmloglist["php-fpm/{$v}-error.log"] = "{$v}-fpm error"; 
		}

		// MR -- only list maillog for mail because change multilog to splogger for qmail-toaster
		$sclist['ffile'] = array(
			'audit/audit.log' => 'Audit',

			'messages' => 'Messages',

			'cron' => 'Cron',

			'secure' => 'Secure',

			'clamav/clamd.log' => 'Clamd',
			'clamav/freshclam.log' => 'Freshclam',

			'letsencrypt/letsencrypt.log' => 'Letsencrypt (certbot)',
			'acme.sh/acme.sh.log' => 'Letsencrypt (acme.sh)',
			'startapi.sh/startapi.sh.log' => 'StartAPI SSL',

			'maillog' => 'Mail log',

			'httpd/access_log' => 'Apache Access',
			'httpd/error_log' => 'Apache Error',

			'lighttpd/access.log' => 'Lighttpd Access',
			'lighttpd/error.log' => 'Lighttpd Error',

			'nginx/access.log' => 'Nginx Access',
			'nginx/error.log' => 'Nginx Error',

			'hiawatha/system.log' => 'Hiawatha System',
			'hiawatha/garbage.log' => 'Hiawatha Garbage',
			'hiawatha/access.log' => 'Hiawatha Access',
			'hiawatha/error.log' => 'Hiawatha Error',
			'hiawatha/exploit.log' => 'Hiawatha Exploit',

			'named/' => 'Named',
			'djbdns.log' => 'DJBDns',
			'nsd.log' => 'NSD',
			'pdns.log' => 'PowerDNS',
			'yadifa/yadifa.log' => 'Yadifa',

			'/var/lib/mysql/mysql-slow.log' => 'MySQL Slow (MariaDB)',
			'/var/lib/mysql/' . gethostname() . '.err' => 'MySQL Error (MariaDB)',
			'mysqld.log' => 'MySQL Log (MySQL)',

			'pureftpd.log' => 'Pure-ftp',

			'rkhunter/rkhunter.log' => 'RKHunter',
			'/usr/local/maldetect/logs/event_log' => 'MalDetect',
			'httpry/httpry.log' => 'Httpry',
			'/var/ossec/logs/alerts/alerts.log' => 'OSSec',

			'/usr/local/lxlabs/kloxo/log/backup' => 'Backup',
			'/usr/local/lxlabs/kloxo/log/restore' => 'Restore',

			'yum.log' => 'Yum',

			'php-fpm/slow.log' => 'php-fpm slow (all)');

		$sclist['ffile'] = array_merge($sclist['ffile'], $phpfpmloglist, $phploglist);

		return $sclist;
	}
}
