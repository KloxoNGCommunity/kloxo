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
		$dbadmin = $this->main->dbadmin_name;
		$dbpass = $this->main->dbpassword;
		$rdb = mysqli_connect('localhost', $dbadmin, $dbpass);

		if (!$rdb) {
			log_error(mysqli_error($rdb));

			throw new lxException('the_mysql_admin_password_is_not_correct', '', '');
		}
	}

	function dosyncToSystemPost()
	{
		dprint("in synctosystem post\n");

		$a['mysql']['dbpassword'] = $this->main->dbpassword;

		slave_save_db("dbadmin", $a);
	}

	function mysql_reset_pass()
	{
		$rdb = $this->lx_mysql_connect("localhost", $this->main->dbadmin_name, $this->main->old_db_password);
		$res = mysqli_query($rdb, "set password=Password('{$this->main->dbpassword}');");

		if (!$res) {
			throw new lxException('mysql_password_reset_failed', '', '');
		}
	}

	function lx_mysql_connect($server, $dbadmin, $dbpass)
	{
		$rdb = mysqli_connect('localhost', $dbadmin, $dbpass);

		if (!$rdb) {
			log_error(mysqli_error($rdb));

			exec_with_all_closed("sh /script/load-wrapper >/dev/null 2>&1 &");

			throw new lxException('could_not_connect_to_db_admin', '', '');
		}

		return $rdb;
	}
}
