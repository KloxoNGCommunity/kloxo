<?php

include_once "lib/html/include.php"; 
initProgram('admin');

setSetupApp();

function setSetupApp()
{
	log_cleanup("*** Afterlogic Webmail Lite setup ***", $nolog);

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

	$path = "/home/kloxo/httpd/webmail/afterlogic";

	exec("mysql -f -u root {$pstring} < {$path}/data/settings/afterlogic_initial.sql >/dev/null 2>&1");

	lxfile_cp("{$path}/data/settings/afterlogic_settings.xml", "{$path}/data/settings/settings.xml");

	$cfgfile = "{$path}/data/settings/settings.xml";

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
