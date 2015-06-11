<?php

include_once "lib/html/include.php"; 
initProgram('admin');

setSetupApp();

function setSetupApp()
{
	log_cleanup("*** Afterlogic Webmail Lite setup ***", $nolog);

	$path = "/home/kloxo/httpd/webmail/afterlogic";
	
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
		return;
	}

	$pstring = null;

	if ($pass) {
		$pstring = "-p\"$pass\"";
	}

	exec("mysql -f -u root {$pstring} < {$path}/data/settings/afterlogic_initial.sql >/dev/null 2>&1");

	if (file_exists("{$path}/data/settings/afterlogic_settings.xml.php")) {
		lxfile_rm("{$path}/data/settings/settings.xml");
		lxfile_cp("{$path}/data/settings/afterlogic_settings.xml.php", "{$path}/data/settings/settings.xml.php");
		$cfgfile = "{$path}/data/settings/settings.xml.php";
	} else {
		lxfile_cp("{$path}/data/settings/afterlogic_settings.xml", "{$path}/data/settings/settings.xml");
		$cfgfile = "{$path}/data/settings/settings.xml";
	}

	log_cleanup("- Generating password", $nolog);
	$pass = randomString(8);
	log_cleanup("- Add Password to configuration file", $nolog);

	$content = lfile_get_contents($cfgfile);
	$content = str_replace("<DBPassword>afterlogic</DBPassword>", "<DBPassword>{$pass}</DBPassword>", $content);
	$content = str_replace("<AdminPassword>afterlogic</AdminPassword>", "<AdminPassword>{$pass}</AdminPassword>", $content);

	lfile_put_contents($cfgfile, $content);

	$result = $link->query("GRANT ALL ON afterlogic.* TO afterlogic@localhost IDENTIFIED BY '{$pass}'");
	$link->query("flush privileges");

	lfile_put_contents($cfgfile, $content);

	if (!$result) {
		print("- Could not grant privileges. Script Abort");
		exit;
	}
}
