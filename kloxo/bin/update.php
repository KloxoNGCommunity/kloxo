<?php 

// include_once "lib/html/include.php";
// include_once "lib/html/updatelib.php";

// exit_if_not_system_user();
// exit_if_another_instance_running();

// update_main();

// MR -- just enough running cleanup!
// have a problem if restart kloxo service and then bypass it.
exec("yum clean all; yum update kloxomr7 -y; sh /script/cleanup-nokloxorestart");

createRestartFile('restart');

