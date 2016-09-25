<?php

class Sendmailban extends Lxdb 
{
	static $__desc = array("", "",  "sendmailban",);
	static $__desc_nname = array("", "",  "sendmailban",);
	static $__desc_target =  array("h", "",  "sendmailban_target");
	static $__desc_syncserver =  array("", "",  "syncserver");

	function isSync()
	{
		return false ;
	}

	static function createListNlist($parent, $view)
	{
		$nlist["parent_clname"] = "30%";
		$nlist["target"] = "30%";
		$nlist["syncserver"] = "100%";

		return $nlist;
	}

	static function createListAlist($parent, $class)
	{
		$alist[] = "a=list&c=sendmailban";

		return $alist;
	}

	static function add($parent, $class, $param)
	{
		$user = $parent->nname;
		$target = '/home/' . $parent->nname . $param['target'];

		if (is_file($target)) {
			$target = dirname($target);
		}

		$param['nname'] = $user . str_replace('/', '_', str_replace("/home/{$user}", '', $target));
		$param['target'] = $target;

		return $param;
	}

	function postAdd()
	{
		$this->write();

		$this->fullUpdate();
	}

	function deleteSpecific()
	{
		$this->write();

		$this->fullUpdate();
	}

	static function AddListForm($parent, $class)
	{
		return self::addform($parent, $class);
	}

	static function addform($parent, $class, $typetd = null)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$vlist['target'] = array('L', '/');
		$ret['variable'] = $vlist;
		$ret['action'] = 'add';
		
		return $ret;
	}

	function fullUpdate()
	{
		$parent = $this->getParentO();
		$d = new Sqlite(null, "sendmailban");
		$r = $d->getRowsWhere("syncserver = '$parent->syncserver'", array('target'));

		$s = '';

		foreach ($r as $k => $v) {
			$s .= $v['target'] . "\n";
			
		}

		file_put_contents('/var/qmail/control/badsendmailfrom', $s);
	}
}

