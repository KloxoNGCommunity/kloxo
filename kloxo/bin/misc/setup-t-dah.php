<?php

include_once "lib/html/include.php"; 
initProgram('admin');

setSetupApp();

function setSetupApp($nolog = null)
{
	log_cleanup("*** T-Dah Webmail setup ***", $nolog);

	$path = "/home/kloxo/httpd/webmail/t-dah";
	
	if (!file_exists("{$path}/index.php")) {
		log_cleanup("- Application not exists. Exit", $nolog);
		return;
	}

	log_cleanup("- Preparing Database", $nolog);
	log_cleanup("-- No need database", $nolog);

	log_cleanup("- Preparing Configs", $nolog);
	lxfile_cp("{$path}/inc/config/t-dah_config.mail.php", "{$path}/inc/config/config.mail.php");
	lxfile_cp("{$path}/inc/config/t-dah_config.paths.php", "{$path}/inc/config/config.paths.php");
	lxfile_cp("{$path}/inc/config/t-dah_config.php", "{$path}/inc/config/config.php");
}
