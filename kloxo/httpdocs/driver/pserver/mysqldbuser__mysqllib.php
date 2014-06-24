<?php

class Mysqldbuser__mysql extends lxDriverClass
{
	function lx_mysql_connect($server, $dbadmin, $dbpass)
	{
		global $login;

		$rdb = new mysqli('localhost', $dbadmin, $dbpass);

		if (!$rdb) {
			log_error($rdb->connect_error);

			exec_with_all_closed("sh /script/load-wrapper >/dev/null 2>&1 &");

			throw new lxException($login->getThrow('could_not_connect_to_db'), '', $rdb->connect_error);
		}

		return $rdb;
	}

	function createUser()
	{
		global $login;

		$rdb = $this->lx_mysql_connect('localhost', $this->main->__var_dbadmin, $this->main->__var_dbpassword);

		$rdb->query("use mysql");

		$res = $rdb->query("select * from user where User = '{$this->main->username}'");
		$ret = $res->fetch_row();

		if ($ret) {
			throw new lxException($login->getThrow('user_already_exists'), '', $this->main->username);
		}

		$rdb->query("grant all on {$this->main->dbname}.* to '{$this->main->username}'@'%' identified by '{$this->main->dbpassword}';");
		$rdb->query("grant all on {$this->main->dbname}.* to '{$this->main->username}'@'localhost' identified by '{$this->main->dbpassword}';");

		$this->log_error_messages(false);

		$rdb->query("flush privileges;");
	}

	function deleteUser()
	{
		$rdb = $this->lx_mysql_connect('localhost', $this->main->__var_dbadmin, $this->main->__var_dbpassword);

		$rdb->query("delete from mysql.user where user = '{$this->main->username}';");

		$this->log_error_messages(false);

		$rdb->query("flush privileges;");
	}

	function updateDatabase()
	{
		$rdb = $this->lx_mysql_connect('localhost', $this->main->__var_dbadmin, $this->main->__var_dbpassword);

		$rdb->query("update mysql.user set password = PASSWORD('{$this->main->dbpassword}') where user = '{$this->main->username}';");

		$this->log_error_messages();

		$rdb->query("flush privileges;");
	}

	function log_error_messages($throwflag = true)
	{
		$rdb = $this->lx_mysql_connect('localhost', $this->main->__var_dbadmin, $this->main->__var_dbpassword);

		if ($rdb->connect_errno) {
			dprint($rdb->connect_error);

			if ($rdb->connect_errno === 1007 && csa($this->main->dbname, "_")) {
				log_message("Mysql Db {$this->main->dbname} already exists. and also has an underscore... Will treat this as the main db..");
				log_error($rdb->connect_error);

				return true;
			}

			if ($throwflag) {
				throw new lxException('mysql_error', '', $rdb->connect_error);
			}
		}
	}

	function doSyncToSystemPre()
	{
		global $gbl, $sgbl, $login, $ghtml;

		databasecore::loadExtension('mysql');
	}

	function dbactionAdd()
	{
		$this->createUser();
	}

	function dbactionDelete()
	{
		$this->deleteUser();
	}

	function dbactionUpdate($subaction)
	{
		$this->updateDatabase();
	}

}
