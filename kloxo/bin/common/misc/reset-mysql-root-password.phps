<?php 

include_once "htmllib/lib/include.php";

if (isset($argv[1])) {
	$pass = $argv[1];
} else {
	$pass = "";
}

pserver__Linux::mysqlPasswordReset($pass);

$user = "root";
$host = "localhost";
$dbname = "kloxo";

$dbconn = mysqli_connect($host, $user, $pass, $dbname);

$string = "update dbadmin set dbpassword = '{$pass}' where syncserver = 'localhost'";

if (mysqli_query($dbconn, $string) === true) {
	print("Password successfully reset to \"$pass\"\n");
} else {
	print("Password unsuccessfully reset\n");
}

mysqli_close($dbconn);


