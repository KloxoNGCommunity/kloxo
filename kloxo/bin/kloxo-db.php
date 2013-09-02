<?php 

include_once "htmllib/lib/include.php"; 

chdir("/usr/local/lxlabs/lxadmin/httpdocs/");
$pass = slave_get_db_pass();
$res = mysqli_connect("localhost", "root", $pass);

$dbadminpass = trim(file_get_contents("/usr/local/lxlabs/lxadmin/etc/conf/lxadmin.pass"));

print($dbadminpass);
print("\n");

mysqli_query($res, "grant all on kloxo.* to 'kloxo'@'localhost' identified by '$dbadminpass';");
mysqli_query($res, "flush privileges");
