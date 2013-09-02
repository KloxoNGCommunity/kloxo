<?php 

include_once "htmllib/lib/include.php";

if (isset($argv[1])) {
	$pass = $argv[1];
} else {
	$pass = "";
}

pserver__Linux::mysqlPasswordReset($pass);

print("Restart Mysql service again\n");

if (file_exists("/etc/rc.d/init.d/mysql")) {
	system("service mysql restart");
} else {
	system("service mysqld restart");
}