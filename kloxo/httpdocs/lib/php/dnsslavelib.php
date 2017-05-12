<?php

class DnsSlave extends Lxdb 
{
	static $__desc = array("", "",  "dnsslave",);
	static $__desc_nname =  array("h", "",  "slave_domain");
	static $__desc_master_ip =  array("", "",  "master_ip");
	static $__desc_syncserver =  array("s", "",  "syncserver");
	static $__desc_serial =  array("", "",  "Serial");

	function isSync()
	{
		return false ;
	}

	static function createListNlist($parent, $view)
	{
		$nlist["parent_clname"] = "15%";
		$nlist["nname"] = "30%";
		$nlist["master_ip"] = "15%";
		$nlist["syncserver"] = "15%";
		$nlist["serial"] = "100%";

		return $nlist;
	}

	static function createListAlist($parent, $class)
	{
		$alist[] = "a=list&c=dnsslave";

		return $alist;
	}

	static function add($parent, $class, $param)
	{
		validate_domain_name($param['nname'], $bypass = true);
		validate_ipaddress($param['master_ip']);

		return $param;
	}

	function postAdd()
	{
		// MR -- need write because have next action for fixdns!
		// TODO: make 'general' action for this process
		$this->write();

		$ip = $this->master_ip;
		$domain = $this->nname;
		$syncserver = $this->syncserver;

		$path = "/opt/configs/dnsslave_tmp";
	/*
		// MR -- no need this dir because read from db directly
		if (!file_exists($path)) {
			lxshell_return("mkdir", "-p", $path);
		}

		exec("echo '{$ip}' > {$path}/{$domain}");
	*/
		// MR -- deleted
		if (file_exists($path)) {
			lxshell_return("rm", "-rf", $path);
		}

		exec("sh /script/fixdns --server={$syncserver}");
	}

	function deleteSpecific()
	{
		// MR -- need write because have next action for fixdns!
		// TODO: make 'general' action for this process
		$this->write();

		$ip = $this->master_ip;
		$domain = $this->nname;
	
		$syncserver = $this->syncserver;
		$path = "/opt/configs/dnsslave_tmp";

		if (!file_exists($path)) {
			lxshell_return("mkdir", "-p", $path);
		}

		exec("'rm' -rf {$path}/{$domain}");
		exec("sh /script/fixdns --server={$syncserver}");
	}

	static function AddListForm($parent, $class)
	{
		return self::addform($parent, $class);
	}

	static function addform($parent, $class, $typetd = null)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$vlist['nname'] = null;
		$vlist['master_ip'] = null;
		$vlist['syncserver'] = array('s', self::getServerList());

		$ret['variable'] = $vlist;
		$ret['action'] = 'add';
		
		return $ret;
	}

	static function getServerList()
	{
		global $login;

		$plist = $login->getList('pserver');

		foreach($plist as $s) {
			$ret[] = $s->nname;
		}

		return $ret;
	}
}

