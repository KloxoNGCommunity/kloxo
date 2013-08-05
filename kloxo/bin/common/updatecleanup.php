<?php 

exit_if_not_system_user();

system("lxphp.exe ../bin/common/tmpupdatecleanup.php {$argv[1]}");
exit;

