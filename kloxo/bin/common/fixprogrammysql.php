<?php 

include_once "lib/html/include.php"; 

if ($argv[1]) {
	$mysqlpass = $argv[1];
} else {
	$mysqlpass = slave_get_db_pass();
}

$db = $sgbl->__var_dbf;
$username = $sgbl->__var_program_name;
$program = $username;
$newpass = randomString(9);
$newpass = client::createDbPass($newpass);

$conn = mysqli_connect("localhost", "root", $mysqlpass);

$cmd = "grant all on $db.* to $username@localhost identified by '$newpass'";

print("$cmd\n");

mysqli_query($conn, $cmd);

lfile_put_contents("../etc/conf/$program.pass", $newpass);



