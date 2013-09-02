<?php

class Mysqldbuser__mysql extends lxDriverClass
{


	function lx_mysql_connect($server, $dbadmin, $dbpass)
	{
		$rdb = mysqli_connect('localhost', $dbadmin, $dbpass);

		if (!$rdb) {
			log_error(mysqli_error($rdb));

			exec_with_all_closed("sh /script/load-wrapper >/dev/null 2>&1 &");

			throw new lxException('could_not_connect_to_db', '', '');
		}

		return $rdb;
	}

	function createUser()
	{
		$rdb = $this->lx_mysql_connect('localhost', $this->main->__var_dbadmin, $this->main->__var_dbpassword);

		mysqli_query($rdb, "use mysql");

		$res = mysqli_query($rdb, "select * from user where User = '{$this->main->username}'");

		$ret = mysqli_fetch_row($res, MYSQLI_NUM);

		if ($ret) {
			throw new lxException('user_already_exists', '', '');
		}

		mysqli_query($rdb, "grant all on {$this->main->dbname}.* to '{$this->main->username}'@'%' identified by '{$this->main->dbpassword}';");
		mysqli_query($rdb, "grant all on {$this->main->dbname}.* to '{$this->main->username}'@'localhost' identified by '{$this->main->dbpassword}';");

		$this->log_error_messages(false);

		mysqli_query($rdb, "flush privileges;");
	}

	function deleteUser()
	{
		$rdb = $this->lx_mysql_connect('localhost', $this->main->__var_dbadmin, $this->main->__var_dbpassword);

		mysqli_query($rdb, "delete from mysql.user where user = '{$this->main->username}';");
		$this->log_error_messages(false);

		mysqli_query($rdb, "flush privileges;");
	}

	function updateDatabase()
	{
		$rdb = $this->lx_mysql_connect('localhost', $this->main->__var_dbadmin, $this->main->__var_dbpassword);

		print("here\n");

		mysqli_query($rdb, "update mysql.user set password = PASSWORD('{$this->main->dbpassword}') where user = '{$this->main->username}';");
		$this->log_error_messages();

		mysqli_query($rdb, "flush privileges;");
	}

	function log_error_messages($throwflag = true)
	{
		$rdb = $this->lx_mysql_connect('localhost', $this->main->__var_dbadmin, $this->main->__var_dbpassword);

		if (mysqli_connect_errno($rdb)) {
			dprint(mysqli_error($rdb));

			if (mysqli_errno($rdb) === 1007 && csa($this->main->dbname, "_")) {
				log_message("Mysql Db {$this->main->dbname} already exists. and also has an underscore... Will treat this as the main db..");
				log_error(mysqli_error($rdb));

				return true;
			}

			if ($throwflag) {
				throw new lxException('mysql_error', '', mysqli_error($rdb));
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
