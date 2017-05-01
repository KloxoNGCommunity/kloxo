<?php

include_once "lib/html/include.php"; 
initProgram('admin');

setSetupApp();

function setSetupApp($nolog = null)
{
	log_cleanup("*** TheHostingTool Billing setup ***", $nolog);

	$path = "/home/kloxo/httpd/cp/tht";
	
	if (!file_exists("{$path}/index.php")) {
		log_cleanup("- Application not exists. Exit", $nolog);
		exit;
	}

	log_cleanup("- Preparing Database", $nolog);

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

	log_cleanup("- Create 'thehostingtool' database", $nolog);
	exec("mysql -f -u root {$pstring} < {$path}/tht_install.sql >/dev/null 2>&1");

	$sfile = getLinkCustomfile($path, "tht_conf.inc.php");
	$tfile = "{$path}/includes/conf.inc.php";

	$content = file_get_contents($sfile);

	log_cleanup("- Generate random password", $nolog);
	$pass = randomString(8);

	log_cleanup("- Assign username database", $nolog);
	$result = $link->query("GRANT ALL ON thehostingtool.* TO thehostingtool@localhost IDENTIFIED BY '{$pass}'");
	$link->query("flush privileges");

	log_cleanup("- Create '/tht/conf.inc.php'", $nolog);
	$content = str_replace("sql['pass'] = 'thehostingtool'", "sql['pass'] = '{$pass}'", $content);

	file_put_contents($tfile, $content);

	$txt = "* Note: Access to 'http://cp.<yourdomain>/tht/admin' with 'admin'\n" .
		"        for username and password (change password immediately)\n";
	print($txt);
}
