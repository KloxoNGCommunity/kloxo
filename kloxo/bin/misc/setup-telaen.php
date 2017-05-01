<?php

include_once "lib/html/include.php"; 
initProgram('admin');

setSetupApp();

function setSetupApp($nolog = null)
{
	log_cleanup("*** Telaen Webmail setup ***", $nolog);

	$path = "/home/kloxo/httpd/webmail/telaen";
	
	if (!file_exists("{$path}/index.php")) {
		log_cleanup("- Application not exists. Exit", $nolog);
		return;
	}

	log_cleanup("- Preparing Database", $nolog);
	log_cleanup("-- No need database", $nolog);

	log_cleanup("- Preparing Configs", $nolog);
	lxfile_cp("{$path}/inc/config/config.php.default", "{$path}/inc/config/config.php");

	$s = file_get_contents("{$path}/inc/config/config.php");
	$s = str_replace("/some/place/safe/smarty/", "./smarty/", $s);
	file_put_contents("{$path}/inc/config/config.php", $s);
	
	if (!file_exists("{$path}/ChangeMe!")) {
		mkdir("{$path}/ChangeMe!");
	}

	lxfile_cp("{$path}/inc/config/config.languages.php.default", "{$path}/inc/config/config.languages.php");
	lxfile_cp("{$path}/inc/config/config.security.php.default", "{$path}/inc/config/config.security.php");
}
