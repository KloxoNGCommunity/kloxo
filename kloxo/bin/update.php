<?php 

include_once "lib/html/include.php";
include_once "lib/html/updatelib.php";

exit_if_not_system_user();
exit_if_another_instance_running();

update_main();



