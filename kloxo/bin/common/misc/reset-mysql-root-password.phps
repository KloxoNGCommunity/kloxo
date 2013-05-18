<?php 

include_once "htmllib/lib/include.php";

if (isset($argv[1])) {
	$pass = $argv[1];
} else {
	$pass = "";
}

pserver__Linux::mysqlPasswordReset($pass);

/*

$user = "root";
$host = "localhost";
$dbname = "kloxo";

$dbconn = mysql_connect($host, $user, $pass, $dbname);

$string = "update dbadmin set dbpassword = '{$pass}' where syncserver = 'localhost'";

if (mysql_query($string, $dbconn) !== false) {
	print("Password successfully reset to \"$pass\"\n");
} else {
	print("Password unsuccessfully reset\n");
}

mysql_close($dbconn);

*/

echo "Restart Mysql service again"
if (file_exists("/etc/rc.d/init.d/mysql")) {
	system("service mysql restart");
} else {
	system("service mysqld restart");
}