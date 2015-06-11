<?php

include_once "lib/html/include.php"; 
initProgram('admin');

setSetupApp();

function setSetupApp()
{
	log_cleanup("*** RoundCube Webmail setup ***", $nolog);

	// MR -- because Roundcube use rpm on Kloxo-MR,
	// so roundcube_mysql.initial.sql and db.inc.php as template

	//  Related to issue #421

	$path = "/home/kloxo/httpd/webmail/roundcube";
	
	if (!file_exists("{$path}/index.php")) {
		log_cleanup("- Application not exists. Exit", $nolog);
		return;
	}

	log_cleanup("- Preparing database", $nolog);

	$pass = slave_get_db_pass();
	$user = "root";
	$host = "localhost";

	$link = new mysqli($host, $user, $pass);

	if (!$link) {
		log_cleanup("- Mysql root password incorrect", $nolog);
		exit;
	}

	$pstring = null;

	if ($pass) {
		$pstring = "-p\"$pass\"";
	}

	log_cleanup("- Fixing MySQL commands in import files", $nolog);

	lxfile_cp("{$path}/SQL/roundcube_mysql.initial.sql", "{$path}/SQL/mysql.initial.sql");

	exec("mysql -f -u root {$pstring} < {$path}/SQL/mysql.initial.sql >/dev/null 2>&1");

	log_cleanup("- Generating password", $nolog);
	$pass = randomString(8);
	log_cleanup("- Add Password to configuration file", $nolog);

	if (file_exists("{$path}/config/roundcube_main.inc.php")) {
		lxfile_cp("{$path}/config/roundcube_main.inc.php", "{$path}/config/main.inc.php");
	}

	if (file_exists("{$path}/config/roundcube_defaults.inc.php")) {
		lxfile_cp("{$path}/config/roundcube_defaults.inc.php", "{$path}/config/defaults.inc.php");
		$cfgfile = "{$path}/config/defaults.inc.php";
		$content = lfile_get_contents($cfgfile);
		$content = str_replace("mysql://roundcube:roundcube", "mysql://roundcube:" . $pass, $content);
		$content = str_replace("mysql://roundcube:pass", "mysql://roundcube:" . $pass, $content);
		$content = str_replace("mysql://roundcube:@", "mysql://roundcube:" . $pass . "@", $content);
		lfile_put_contents($cfgfile, $content);
	}

	if (file_exists("{$path}/config/roundcube_db.inc.php")) {
		lxfile_cp("{$path}/config/roundcube_db.inc.php", "{$path}/config/db.inc.php");
		$cfgfile = "{$path}/config/db.inc.php";
		$content = lfile_get_contents($cfgfile);
		$content = str_replace("mysql://roundcube:roundcube", "mysql://roundcube:" . $pass, $content);
		$content = str_replace("mysql://roundcube:pass", "mysql://roundcube:" . $pass, $content);
		$content = str_replace("mysql://roundcube:@", "mysql://roundcube:" . $pass . "@", $content);
		lfile_put_contents($cfgfile, $content);
	}

	if (file_exists("{$path}/config/roundcube_config.inc.php")) {
		lxfile_cp("{$path}/config/roundcube_config.inc.php", "{$path}/config/config.inc.php");
		$cfgfile = "{$path}/config/config.inc.php";
		$content = lfile_get_contents($cfgfile);
		$content = str_replace("mysql://roundcube:roundcube", "mysql://roundcube:" . $pass, $content);
		$content = str_replace("mysql://roundcube:pass", "mysql://roundcube:" . $pass, $content);
		$content = str_replace("mysql://roundcube:@", "mysql://roundcube:" . $pass . "@", $content);
		lfile_put_contents($cfgfile, $content);
	}


	$result = $link->query("GRANT ALL ON roundcubemail.* TO roundcube@localhost IDENTIFIED BY '{$pass}'");
	$link->query("flush privileges");

	if (!$result) {
		print("- Could not grant privileges. Script Abort");

		exit;
	}

	log_cleanup("- Database installed", $nolog);
	$pass = null;
	$pstring = null;

	//--- to make sure always 644
	if (file_exists("{$path}/config/roundcube_defaults.inc.php")) {
		lxfile_unix_chmod("{$path}/config/defaults.inc.php", "644");
	}

	if (file_exists("{$path}/config/roundcube_db.inc.php")) {
		lxfile_unix_chmod("{$path}/config/db.inc.php", "644");
	}

	// MR -- update database
	$sqlfiles = glob("{$path}/SQL/mysql/*.sql");

	foreach ($sqlfiles as $k => $v) {
		exec("mysql -f -u root {$pstring} < {$v} >/dev/null 2>&1");
	}
}

