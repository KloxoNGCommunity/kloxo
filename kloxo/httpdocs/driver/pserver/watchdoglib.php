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
	//	$vlist['port'] = array('M', null);
		$vlist['port'] = null;

	//	if ($this->isOn('added_by_system')) {
	//		$vlist['action'] = array('M', null);
	//	} else {
			$vlist['action'] = null;
	//	}

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

/*
	// MR -- disabled because want able to deleted from list
	static function createListBlist($parent, $class)
	{
		return null;
	}
*/
	static function addDefaultWatchdog($pserver)
	{
		$v = new watchdog(null, $pserver, "mysql___$pserver");
		$v->get();

		if ($v->dbaction !== 'add') {
			$v->setUpdateSubaction('update');
			$v->was();

			return;
		}

		self::addOneWatchdog($pserver, "dns", "53", "__driver_dns");
		self::addOneWatchdog($pserver, "web", "80", "__driver_web");
		self::addOneWatchdog($pserver, "mail", "25", "__driver_qmail");
		self::addOneWatchdog($pserver, "mysql", "pgrep ^mysql", "__driver_mysql");
		self::addOneWatchdog($pserver, "ftp", "pgrep ^pure-ftpd", "__driver_ftp");
		self::addOneWatchdog($pserver, "php-fpm", "pgrep ^php-fpm", "__driver_php-fpm");

		self::addOneWatchdog($pserver, "webproxy", "30080", "__driver_web", "off");
		self::addOneWatchdog($pserver, "webcache", "8080", "__driver_web", "off");
	}

	static function addOneWatchdog($pserver, $service, $port, $command, $status = null)
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
		$v->status = ($status) ? $status : "on";
		$v->added_by_system = "on";
		$v->syncserver = $pserver;
		$v->parent_clname = createClName('pserver', $pserver);
		$v->dbaction = 'add';
		$v->createExtraVariables();
		$v->was();
	}

	static function AddListForm($parent, $class)
	{
		return self::addform($parent, $class);
	}

	static function addform($parent, $class, $typetd = null)
	{

		$vlist['servicename'] = null;
		$vlist['status'] = null;
		$vlist['port'] = null;
		$vlist['action'] = null;

		$ret['action'] = 'add';
		$ret['variable'] = $vlist;

		return $ret;
	}
}
