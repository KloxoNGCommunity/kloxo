<?php

include_once "lib/html/include.php"; 
initProgram('admin');

setSetupApp();

function setSetupApp($nolog = null)
{
	log_cleanup("*** Horde Webmail setup ***", $nolog);

	$path = "/home/kloxo/httpd/webmail/horde";
	
	if (!file_exists("{$path}/index.php")) {
		log_cleanup("- Application not exists. Exit", $nolog);
		return;
	}

	// MR -- because Horde use rpm on Kloxo-MR,
	// so horde_groupware.sql and horde_conf.php as template

	log_cleanup("- Preparing database", $nolog);

	$pass = slave_get_db_pass();
	$user = "root";
	$host = "localhost";

	$link = new mysqli($host, $user, $pass);

	if (!$link) {
		log_cleanup("- Mysql root password incorrect", $nolog);
		return;
	}

	$pstring = null;

	if ($pass) {
		$pstring = "-p\"$pass\"";
	}

	$result = $link->select_db('horde_groupware');

	log_cleanup("- Fix MySQL commands in import files of Horde", $nolog);

	lxfile_cp("{$path}/scripts/sql/horde_groupware.mysql.sql", "{$path}/scripts/sql/groupware.mysql.sql");

	$hordefile = "/home/kloxo/httpd/webmail/horde/scripts/sql/groupware.mysql.sql";

	exec("mysql -f -u root {$pstring} < {$path}/scripts/sql/groupware.mysql.sql >/dev/null 2>&1");

	lxfile_cp("{$path}/config/horde_conf.php", "{$path}/config/conf.php");

	$cfgfile = "{$path}/config/conf.php";

	log_cleanup("- Generating password", $nolog);
	$pass = randomString(8);
	log_cleanup("- Add password to configuration file", $nolog);

	$content = lfile_get_contents($cfgfile);
	$content = str_replace("conf['sql']['password'] = 'horde';", "conf['sql']['password'] = '{$pass}';", $content);

	lfile_put_contents($cfgfile, $content);

	$result = $link->query("GRANT ALL ON horde_groupware.* TO horde_groupware@localhost IDENTIFIED BY '{$pass}'");
	$link->query("flush privileges");

	if (!$result) {
		log_cleanup("Could not grant privileges. Script Aborted", $nolog);
		exit;
	}

	log_cleanup("- Database installed", $nolog);

	$pass = null;
	$pstring = null;

	//--- to make sure always 644
	lxfile_unix_chmod("/home/kloxo/httpd/webmail/horde/config/conf.php", "644");

}

