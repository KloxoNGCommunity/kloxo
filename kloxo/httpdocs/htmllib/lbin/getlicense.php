<?php 
if (!file_exists("lib/html/displayinclude.php")) {
	chdir("../..");
}
include_once "lib/html/displayinclude.php";

if (!os_isSelfSystemOrLxlabsUser()) {
	exit;
}
initProgram('admin');
license::doupdateLicense();
print("License Successfully updated\n");
