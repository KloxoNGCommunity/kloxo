<?php 
include_once "lib/html/include.php";
include_once "lib/html/updatelib.php";

print("Getting Version list\n");
$v = getVersionList();
print_r($v);
if ($v[0] === $sgbl->__ver_major_minor_release) {
	print("Hey Same version\n");
	exit(8);
}
exit(0);

