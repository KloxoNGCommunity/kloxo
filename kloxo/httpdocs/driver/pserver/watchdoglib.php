<?php

class watchdog extends lxdb
{
	static $__desc = array("", "", "service");
	static $__desc_port = array("", "", "port");
	static $__desc_servicename = array("", "", "servicename", "a=show");
	static $__desc_action = array("", "", "action");
	static $__desc_status = array("ef", "", "status");
	static $__desc_status_v_on = array("", "", "enabled");
	static $__desc_status_v_off = array("", "", "disabled");
	static $__rewrite_nname_const = Array("servicename", "syncserver");
	static $__acdesc_list = array("", "", "watchdog");
	static $__acdesc_update_update = array("", "", "edit");

	function createExtraVariables()
	{
		$sq = new Sqlite(null, 'watchdog');
		$this->__var_watchlist = $sq->getRowsWhere("syncserver = '$this->syncserver'");
	}

	function createShowUpdateform()
	{
		$uflist['update'] = null;
		
		return $uflist;
	}

	function getId()
	{
		return strtil($this->nname, "___");
	}

	static function createListAlist($parent, $class)
	{
		$alist[] = "a=list&c=$class";

		return $alist;
	}

	function updateform($subaction, $param)
	{
		$vlist['servicename'] = array('M', null);
		$vlist['status'] = null;
		$vlist['port'] = array('M', null);

		if ($this->isOn('added_by_system')) {
			$vlist['action'] = array('M', null);
		} else {
			$vlist['action'] = null;
		}

		return $vlist;
	}

	static function createListNlist($parent, $view)
	{
		$nlist['status'] = '5%';
		$nlist['servicename'] = '20%';
		$nlist['port'] = '10%';
		$nlist['action'] = '65%';

		return $nlist;
	}

	static function createListBlist($parent, $class)
	{
		return null;
	}

	static function addDefaultWatchdog($pserver)
	{
		$v = new watchdog(null, $pserver, "mysql___$pserver");
		$v->get();

		if ($v->dbaction !== 'add') {
			$v->setUpdateSubaction('update');
			$v->was();

			return;
		}
	/*
		self::addOneWatchdog($pserver, "smtp", "25", "/etc/init.d/xinetd restart");
		self::addOneWatchdog($pserver, "smtp", "25", "/etc/init.d/qmail restart");
		self::addOneWatchdog($pserver, "ftp", "21", "/etc/init.d/xinetd restart");
		self::addOneWatchdog($pserver, "pop", "110", "/etc/init.d/courier-imap restart");
		self::addOneWatchdog($pserver, "imap", "143", "/etc/init.d/courier-imap restart");
		self::addOneWatchdog($pserver, "pop", "110", "/etc/init.d/dovecot restart");
		self::addOneWatchdog($pserver, "imap", "143", "/etc/init.d/dovecot restart");
		self::addOneWatchdog($pserver, "mysql", "3306", "/etc/init.d/mysqld restart");
		self::addOneWatchdog($pserver, "mariadb", "3306", "/etc/init.d/mysql restart");

		// MR -- no need special handling for php-fpm but including in __driver_web
		// on lxserverlib.php
	
		// MR -- watchdog for php-fpm for listening 'apache' user port
		$userinfo = posix_getpwnam('apache');
		$fpmport = (50000 + $userinfo['uid']);
		if (file_exists('/etc/init.d/php-fpm')) {
			self::addOneWatchdog($pserver, "php-fpm", $fpmport, "/etc/init.d/php-fpm restart");
		}
	*/

		self::addOneWatchdog($pserver, "dns", "53", "__driver_dns");
		self::addOneWatchdog($pserver, "web", "80", "__driver_web");
		self::addOneWatchdog($pserver, "mail", "25", "/etc/init.d/qmail restart");
	//	self::addOneWatchdog($pserver, "mysql", "3306", "__driver_mysql");
		self::addOneWatchdog($pserver, "mysql", "3306", "/etc/init.d/mysqld restart");
		self::addOneWatchdog($pserver, "mariadb", "3306", "/etc/init.d/mysql restart");
		self::addOneWatchdog($pserver, "ftp", "21", "/etc/init.d/xinetd restart");
	}

	static function addOneWatchdog($pserver, $service, $port, $command)
	{
		$v = new watchdog(null, $pserver, "{$service}___$pserver");
		$v->get();

		if ($v->dbaction !== 'add') {
			dprint("$service $pserver already exists...\n");
			return;
		}

		$v->servicename = $service;
		$v->port = $port;
		$v->action = $command;
		$v->status = "on";
		$v->added_by_system = "on";
		$v->syncserver = $pserver;
		$v->parent_clname = createClName('pserver', $pserver);
		$v->dbaction = 'add';
		$v->createExtraVariables();
		$v->was();
	}
}
