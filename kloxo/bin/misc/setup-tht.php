<?php

include_once "lib/html/include.php"; 
initProgram('admin');

setSetupApp();

function setSetupApp()
{
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

	$path = "/home/kloxo/httpd/cp/tht";

	exec("mysql -f -u root {$pstring} < {$path}/tht_install.sql >/dev/null 2>&1");

	$sfile = getLinkCustomfile($path, "tht_conf.inc.php");
	$tfile = "{$path}/includes/conf.inc.php";

	$content = file_get_contents($sfile);

	log_cleanup("- Generating password", $nolog);
	$pass = randomString(8);

	$result = $link->query("GRANT ALL ON thehostingtool.* TO thehostingtool@localhost IDENTIFIED BY '{$pass}'");
	$link->query("flush privileges");

	$content = str_replace("sql['pass'] = 'thehostingtool'", "sql['pass'] = '{$pass}'", $content);

	file_put_contents($tfile, $content);
}
