<?php

include_once "lib/html/include.php"; 
initProgram('admin');

setSetupApp();

function setSetupApp()
{
	log_cleanup("*** Telaen Webmail setup ***", $nolog);

	log_cleanup("- Preparing Database", $nolog);
	log_cleanup("-- No need database", $nolog);

	$path = "/home/kloxo/httpd/webmail/telaen";

	log_cleanup("- Preparing Configs", $nolog);
	lxfile_cp("{$path}/inc/config/config.php.default", "{$path}/inc/config/config.php");
	lxfile_cp("{$path}/inc/config/config.languages.php.default", "{$path}/inc/config/config.languages.php");
	lxfile_cp("{$path}/inc/config/config.security.php.default", "{$path}/inc/config/config.security.php");
}
