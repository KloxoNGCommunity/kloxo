<?php

class PhpModule extends lxclass
{
	// Data

	static $__desc = array("", "",  "phpmodule");

	static $__desc_nname = array("", "",  "phpmodule_name");
	static $__desc_type = array("", "",  "phpmodule_type");
	static $__desc_target = array("", "",  "phpmodule_target");
	static $__desc_modulename = array("", "",  "phpmodule_name");
	static $__desc_fullfile = array("", "",  "phpmodule_fullfile");

	static $__desc_status = array("eS", "",  "s:status");
	static $__desc_status_v_on = array("eS", "",  "is_installed");
	static $__desc_status_v_off = array("eS", "",  "is_not_installed");

//	static $__rewrite_nname_const = array("modulename", "syncserver");

	static $__desc_button_enable_f = array("b", "", "", "a=update&sa=enable");
	static $__desc_button_disable_f = array("b", "", "", "a=update&sa=disable");
	static $__desc_button_restart_f = array("b", "", "", "a=update&sa=restart");

	static $__acdesc_update_enable = array("", "", "enable");
	static $__acdesc_update_disable = array("", "", "disable");
	static $__acdesc_update_restart = array("", "", "restart");

	static $__acdesc_list = array("", "",  "phpmodule_status");

//	static $__acdesc_update_restart = array("", "",  "phpmodule_restart");

	function get() { }

	function write() { }

	function isSelect()	{ return false; }

	static function createListAlist($parent, $class)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$alist[] = "a=list&c=$class";

		return $alist;
	}

	static function createListBlist($parent, $class)
	{
		global $gbl, $sgbl, $login, $ghtml;

	//	$blist[] = array("a=update&sa=restart&c=$class", 1);

	//	return $blist;
	}

	static function createListNlist($parent, $view)
	{
		$nlist['status'] = '3%';
	//	$nlist['nname'] = '25%';
		$nlist['modulename'] = '15%';
		$nlist['target'] = '10%';
		$nlist['type'] = '10%';
		$nlist['fullfile'] = '100%';

		$nlist["button_enable_f"] = '5%';
		$nlist["button_disable_f"] = '5%';
		$nlist["button_restart_f"] = '5%';

		return $nlist;
	}

	static function perPage()
	{
		return 80;
	}

	function createShowUpdateform()
	{
		$uform['update'] = null;

		return $uform;
	}

	static function searchVar()
	{
		return "nname";
	}

	static function initThisObject($parent, $class, $name = null)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$o = new PhpModule($parent->__masterserver, $parent->__readserver, $name);

		return $o;
	}

	static function canGetSingle() { return false; }

	static function initThisListRule($parent, $class) { return null; }

	static function initThisList($parent, $class)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$driverapp = $gbl->getSyncClass($parent->__masterserver, $parent->__readserver, 'phpmodule');

		$res = rl_exec_get($parent->__masterserver, $parent->__readserver,  array("phpmodule__$driverapp", "getListDetail"), array($parent->syncserver));

		return $res;
	}

	function updateform($subaction, $param)
	{
	}

	function update($subaction, $param)
	{
		global $login;

		switch ($subaction) {
			case "enable":
				if ($this->status === 'off') {
					$s = $this->fullfile;

					if (strrpos($s, '_unused.nonini') !== false) {
						$t = str_replace("_unused.nonini", ".ini", $s);
					} else {
						$t = str_replace(".nonini", "_used.ini", $s);
					}

					exec("'mv' -f {$s} {$t}");
				}

			//	throw new lxException($login->getThrow('enable'), '', $subaction);
				break;
			case "disable":
				if ($this->status === 'on') {
					$s = $this->fullfile;

					if (strrpos($s, '_used.ini') !== false) {
						$t = str_replace("_used.ini", ".nonini", $s);
					} else {
						$t = str_replace(".ini", "_unused.nonini", $s);
					}

					exec("'mv' -f {$s} {$t}");
				}

			//	throw new lxException($login->getThrow('disable'), '', $subaction);
				break;
			case "restart":
				createRestartFile("restart-php-fpm");
				createRestartFile("kloxo-php");
				break;
		}

		return $param;
	}

}