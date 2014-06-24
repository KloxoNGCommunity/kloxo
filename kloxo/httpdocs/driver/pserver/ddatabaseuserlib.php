<?php

class dbpermission_b extends Lxaclass
{
	static $__desc_nname =  array("n", "",  "client_name", "a=show");

}

class dbhostlist_a extends Lxaclass
{
}

class databaseusercorelib extends lxdb
{

	static $__desc = array("", "",  "database_user");
	static $__desc_nname =  array("n", "",  "database_user_name", "a=show");
	static $__desc_username = array("n", "",  "database_user_name", URL_SHOW);
	static $__desc_dbtype = array("", "",  "database_type");
	static $__desc_syncserver = array("", "",  "database_server");
	static $__desc_dbpassword = array("n", "",  "password");
//	static $__desc_ddatabase_usage = array("q", "",  "database_disk_usage_(MB)");

	static $__acdesc_update_update = array("", "",  "edit_db");
	static $__acdesc_update_phpmyadmin = array("", "",  "phpmyadmin");

	function createExtraVariables()
	{
		$parent = $this->getParentO();
		
		if (!isset($this->__var_enc_pass)) {
			$this->__var_enc_pass = md5($this->dbpassword);
		}
	}

	static function createListNlist($parent, $view)
	{
		$nlist['nname'] = '20%';
		$nlist['username'] = '80%';
		
		return $nlist;
	}

	function createShowUpdateform()
	{
		$vlist['update'] = null;
		
		return $vlist;
	}
	static function add($parent, $class, $param)
	{

		$param['nname'] = databasecore::getDbName($parent->getParentName(), $param['nname']);
		$param['username'] = $param['nname'];
		$param['dbname'] = $parent->dbname;
		$param['syncserver'] = $parent->syncserver;

		return $param;
	}

	function postAdd()
	{
		global $login;

		$parent = $this->getParentO();
		$nname = $this->username;
		
		if (exists_in_db($parent->__masterserver, 'mysqldb', $nname)) {
			throw new lxException($login->getThrow('database_user_already_exists'), '', $nname);
		}

	}

	static function createListAddForm($parent, $class)
	{
		return true;
	}

	static function createListAlist($parent, $class)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$alist[] = 'a=show';

		return $alist;
	}

	static function AddListForm($parent, $class)
	{
		return self::addform($parent, $class);
	}

	static function addform($parent, $class, $typetd = null)
	{
		$dbprefix = databasecore::fixDbname($parent->getParentName());

		$vlist['dbtype'] === 'mysql';
		$vlist['nname'] = array('m', array('pretext' => $dbprefix));
		$vlist['dbpassword'] = null;

		$res['variable'] = $vlist;
		$res['action'] = 'add';
		
		return $res;
	}

	function updateform($subaction, $param)
	{
		$vlist['nname'] = array('M', $this->nname);
		$vlist['dbpassword'] = null;
		
		return $vlist;
	}
}

class mysqldbuser extends databaseusercorelib {

}

class mssqldbuser extends databaseusercorelib {

}
