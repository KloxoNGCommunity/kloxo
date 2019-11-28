<?php 

include_once "lib/html/include.php";

$db = $sgbl->__var_dbf;
$username = $sgbl->__var_program_name;
$program = $username;

if ($argv[1]) {
	$mysqlpass = $argv[1];
} else {
	$mysqlpass = randomString(9);
}

client::createDbPass($mysqlpass);

$rootpass = slave_get_db_pass();

$conn = new mysqli('localhost', 'root', $rootpass, 'mysql');

$cmd = "grant all on {$db}.* to {$username}@localhost identified by '{$mysqlpass}'";

print($cmd . "\n");

$result = $conn->query($cmd);

if (!$result) {
	print($conn->connect_errno . "\n");
	exit();
}

$conn->close();

file_put_contents("../etc/conf/$program.pass", $mysqlpass);

