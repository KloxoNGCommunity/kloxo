<?php

class BlockedIp extends Lxdb 
{
	static $__desc = array("", "",  "blocked_ip",);
	static $__desc_nname   =  array("", "",  "blockedip");
	static $__desc_ipaddress =  array("n", "",  "blocked_ip");
	static $__rewrite_nname_const = array("ipaddress", "parent_clname");

	function isSync()
	{
		if_demo_throw_exception('ip');
		
		return false ;
	}

	static function createListNlist($parent, $view)
	{
		$nlist["ipaddress"] = "100%";
		
		return $nlist;
	}

	static function createListAlist($parent, $class)
	{
	//	return allowedip::createListAlist($parent, $class);

		$alist[] = "a=list&c=allowedip";
	//	$alist[] = "a=addform&c=allowedip";
		$alist[] = "a=list&c=blockedip";
	//	$alist[] = "a=addform&c=blockedip";

		return $alist;
	}

	static function AddListForm($parent, $class)
	{
		return self::addform($parent, $class);
	}

	static function addform($parent, $class, $typetd = null)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$vlist['ipaddress'] = null;
		$ret['variable'] = $vlist;
		$ret['action'] = 'add';
		
		return $ret;
	}
}

class LoginAttempt extends Lxdb 
{
	static $__desc_nname =  array("", "",  "device_name");

	static function initThisListRule($parent, $class)
	{
		return "__v_table";
	}
}

