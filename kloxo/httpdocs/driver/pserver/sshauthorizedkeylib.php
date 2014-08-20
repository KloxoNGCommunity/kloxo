<?php

class sshauthorizedkey extends lxclass 
{
	static $__desc = array("", "",  "SSH_authorized_key");
	static $__desc_nname = array("n", "",  "key");
	static $__desc_key = array("StW", "",  "key");
	static $__desc_type = array("n", "",  "type");
	static $__desc_hostname = array("n", "",  "hostname");
	static $__desc_type_v_snormal = array("", "",  "authorized_key");
	static $__desc_full_key = array("t", "",  "full_key");

	function get() {}
	
	function write() {}

	static function createListAlist($parent, $class)
	{
		$alist[] = "a=list&c=$class";
	//	$alist[] = "a=addform&c=$class&dta[var]=type&dta[val]=snormal";
		
		return $alist;
	}

	static function getTextAreaProperties($var)
	{
		return array("height" => 30, "width" => "90%");
	}

	static function addListForm($parent, $class)
	{
		return self::addform($parent, $class);
	}

	static function addform($parent, $class, $typetd = null)
	{
		$x = sshauthorizedkey__sync::getAuthorizedKey($parent->username, 'proto');
		$y = $x[key($x)];

		$vlist['full_key'] = array("t", $y['full_key']);
		$ret['variable'] = $vlist;
		$ret['action'] = 'add';

		return $ret;
	}

	static function add($parent, $class, $param)
	{
		$param['full_key'] = trim($param['full_key']);
		
		return $param;
	}

	function postAdd()
	{
		$parent = $this->getParentO();
		$this->username = $parent->username;
		$this->syncserver = $parent->syncserver;
		$this->nname = fix_nname_to_be_variable_without_lowercase($this->full_key);
	}

	static function createListNlist($parent, $view)
	{
		global $gbl, $sgbl, $login, $ghtml;
		
		$nlist['hostname'] = '5%';
		$nlist['type'] = '3%';
		$nlist['key'] = '100%';
		
		return $nlist;
	}

	static function initThisListRule($parent, $class) { return null; }

	static function initThisList($parent, $class)
	{
		$slave = $parent->nname;

	//	if ($parent->is__table('client')) {
		if ($parent->getClass() === 'client') {
			$slave = $parent->websyncserver;
		}

	//	if ($parent->is__table('vps')) {
		if ($parent->getClass() === 'vps') {
			$slave = $parent->syncserver;
		}


	//	$res = rl_exec_get(null, $slave, array('sshauthorizedkey__sync', 'getAuthorizedKey'), array($parent->username));
		$res = sshauthorizedkey__sync::getAuthorizedKey($parent->username);

		if (!$res) { return; }

		foreach($res as &$r) {
			$r['nname'] = "{$slave}___{$r['nname']}";
			$r['syncserver'] = $slave;
			$r['parent_clname'] = $parent->getClName();
		}
		
		return $res;
	}
}
