<?php

include_once "lib/html/include.php"; 
initProgram('admin');

setSetupApp();

function setSetupApp()
{
	log_cleanup("*** Squirrelmail Webmail setup ***", $nolog);

	log_cleanup("- Preparing Database", $nolog);
	log_cleanup("-- No need database", $nolog);
}
