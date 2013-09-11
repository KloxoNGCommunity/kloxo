<?php 

if ($sgbl->is_this_slave()) {
	exit;
}

$dbadmin = new Dbadmin(null, $server, "mysql___localhost");
$dbadmin->get();

$pass = $dbadmin->get();

$rd = new mysqli("localhost", "root", $pass);

if (!$rd) {
	system("lxphp.exe ../bin/common/misc/reset-mysql-root-password.php {$pass}");
}

$rd =  new mysqli("localhost", "root", $pass);




