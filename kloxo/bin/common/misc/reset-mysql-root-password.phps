<?php 

include_once "htmllib/lib/include.php";

if (isset($argv[1])) {
	$pass = $argv[1];
} else {
	$pass = "";
}

pserver__Linux::mysqlPasswordReset($pass);

print("Password successfully reset to \"$pass\"\n");
