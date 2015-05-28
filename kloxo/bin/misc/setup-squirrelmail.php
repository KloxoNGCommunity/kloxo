<?php

include_once "lib/html/include.php"; 
initProgram('admin');

setSetupApp();

function setSetupApp()
{
	log_cleanup("*** Squirrelmail Webmail setup ***", $nolog);

	$path = "/home/kloxo/httpd/webmail/squirrelmail";
	
	if (!file_exists("{$path}/index.php")) {
		log_cleanup("- Application not exists. Exit", $nolog);
		exit;
	}

	log_cleanup("- Preparing Database", $nolog);
	log_cleanup("-- No need database", $nolog);
}
