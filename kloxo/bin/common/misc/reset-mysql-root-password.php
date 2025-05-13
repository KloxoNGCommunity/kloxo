<?php

include_once "lib/html/include.php";

$tpath = "/usr/local/lxlabs/kloxo/serverfile";

if (isset($argv[1])) {
	$pass = $argv[1];
} else {
	$pass = randomString(9);
}

//$text = <<<EOF
//SET PASSWORD FOR root@localhost = PASSWORD('PASSWORD');
//FLUSH PRIVILEGES;
//EOF;


$text = "ALTER USER 'root'@'localhost' IDENTIFIED BY 'PASSWORD';";

echo $pass."\n";
$text = str_replace("'USER'", "'root'", $text);
$text = str_replace("'PASSWORD'", "'{$pass}'", $text);
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
echo "1\n";
//system("mariadbd-safe --skip-grant-tables  >/dev/null 2>&1 &");
system("mariadbd  --user=mysql --init-file={$tpath}/reset-mysql-password.sql >/dev/null 2>&1 &");
echo "2\n";
//system("mysql -u root < {$tpath}/reset-mysql-password.sql");
sleep(15);
echo "3\n";
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

$cmd = "grant all on .* to {$username}@localhost identified by '{$mysqlpass}'";

$cmd = "UPDATE kloxo.dbadmin SET dbpassword = '$pass' WHERE dbadmin_name = 'root'";

//print($cmd . "\n");

$result = $conn->query($cmd);


$conn->close();

$a['mysql']['dbpassword'] = $pass;

slave_save_db("dbadmin", $a);