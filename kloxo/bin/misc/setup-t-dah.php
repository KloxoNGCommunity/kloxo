<?php

include_once "lib/html/include.php"; 
initProgram('admin');

setSetupApp();

function setSetupApp()
{
	log_cleanup("*** T-Dah Webmail setup ***", $nolog);

	log_cleanup("- Preparing Database", $nolog);
	log_cleanup("-- No need database", $nolog);

	$tdahpath = "/home/kloxo/httpd/webmail/t-dah";

	log_cleanup("- Preparing Configs", $nolog);
	lxfile_cp("{$tdahpath}/inc/config/t-dah_config.mail.php", "{$tdahpath}/inc/config/config.mail.php");
	lxfile_cp("{$tdahpath}/inc/config/t-dah_config.paths.php", "{$tdahpath}/inc/config/config.paths.php");
	lxfile_cp("{$tdahpath}/inc/config/t-dah_config.php", "{$tdahpath}/inc/config/config.php");
}
