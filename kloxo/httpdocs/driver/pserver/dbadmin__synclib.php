<?php

class Dbadmin__sync extends lxDriverClass
{
	function dbactionUpdate($subaction)
	{
		switch ($this->main->dbtype) {
			case "mysql":
				$this->mysql_reset_pass();

				break;
		}
	}

	function dbactionAdd()
	{
		global $login;

		$dbadmin = $this->main->dbadmin_name;
		$dbpass = $this->main->dbpassword;

		$rdb = new mysqli('localhost', $dbadmin, $dbpass);

		if (!$rdb) {
			log_error($rdb->connect_error);

			throw new lxException($login->getThrow('mysql_admin_password_is_not_correct'), '', $dbadmin);
		}
	}

	function dosyncToSystemPost()
	{
		$a['mysql']['dbpassword'] = $this->main->dbpassword;

		slave_save_db("dbadmin", $a);
	}

	function mysql_reset_pass()
	{
		global $login;

		$rdb = $this->lx_mysql_connect("localhost", $this->main->dbadmin_name, $this->main->old_db_password);

	//	$res = $rdb->query("set password=Password('{$this->main->dbpassword}');");
		$res = $rdb->query("UPDATE mysql.user SET Password=PASSWORD('{$this->main->dbpassword}') WHERE User='{$this->main->dbadmin_name}';");

		if (!$res) {
			throw new lxException($login->getThrow('mysql_password_reset_failed'), '', $this->main->dbadmin_name);
		}
	}

	function lx_mysql_connect($server, $dbadmin, $dbpass)
	{
		global $login;

		$rdb = new mysqli('localhost', $dbadmin, $dbpass);

		if (!$rdb) {
			log_error($rdb->connect_error);

			exec_with_all_closed("sh /script/load-wrapper >/dev/null 2>&1 &");

			throw new lxException($login->getThrow('could_not_connect_to_db_admin'), '', $dbadmin);
		}

		return $rdb;
	}
}

