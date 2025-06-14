<?php

include_once "lib/html/include.php";

$tpath = "/usr/local/lxlabs/kloxo/serverfile";

if (isset($argv[1])) {
	$pass = $argv[1];
} else {
	$pass = randomString(9);
}

$text = "ALTER USER 'root'@'localhost' IDENTIFIED VIA mysql_native_password USING PASSWORD('PWORD123') OR unix_socket;";

$text = str_replace("'USER'", "'root'", $text);
$text = str_replace("'PWORD123'", "'{$pass}'", $text);
if(!is_dir($tpath)) mkdir($tpath);
file_put_contents("{$tpath}/reset-mysql-password.sql", $text);

print("Stop MySQL/mariadb service...\n");
if (isServiceExists('mariadb')) {
	exec("service mysql stop");
} elseif(isServiceExists('mysqld')) {
	exec("service mysqld stop");
} else {
	exec("service mysql stop");
}
system("killall mariadbd");
print("MySQL ROOT password reset...\n");
sleep(10);

//system("mariadbd-safe --skip-grant-tables  >/dev/null 2>&1 &");
system("mariadbd  --user=mysql --init-file={$tpath}/reset-mysql-password.sql >/dev/null 2>&1 &");

//system("mysql -u root < {$tpath}/reset-mysql-password.sql");
sleep(15);

//system("mysqladmin -u root -p='{$pass}' shutdown");
print("Start MySQL service...\n");
system("killall mariadbd");
exec("'rm' -f {$tpath}/reset-mysql-password.sql");
if (isServiceExists('mariadb')) {
	exec("service mariadb start");
} elseif(isServiceExists('mysqld')) {
	exec("service mysqld start");
} else {
	exec("service mysql start");
}



$conn = new mysqli('localhost', 'root', $pass, 'mysql');

if ($conn->connect_errno) {
	printf("Connect failed: %s\n", $conn->connect_error);

	exit();
}

$cmd = "UPDATE kloxo.dbadmin SET dbpassword = '$pass' WHERE dbadmin_name = 'root'";



$result = $conn->query($cmd);


$conn->close();

$a['mysql']['dbpassword'] = $pass;

slave_save_db("dbadmin", $a);