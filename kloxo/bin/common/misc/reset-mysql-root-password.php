<?php 

include_once "lib/html/include.php";

$tpath = "/usr/local/lxlabs/kloxo/serverfile";

if (isset($argv[1])) {
	$pass = $argv[1];
} else {
	$pass = randomString(9);
}

$text = <<<EOF
FLUSH PRIVILEGES;
SET PASSWORD FOR root@localhost = PASSWORD('PASSWORD');
FLUSH PRIVILEGES;
EOF;

$text = str_replace("'USER'", "'root'", $text);
$text = str_replace("'PASSWORD'", "'{$pass}'", $text);
if(!is_dir($tpath)) mkdir($tpath);
file_put_contents("{$tpath}/reset-mysql-password.sql", $text);

print("Stop MySQL/mariadb service...\n");
if (isServiceExists('mariadb')) {
	exec("service mysqld stop");
} elseif(isServiceExists('mysqld')) {
	exec("service mysqld stop");
} else {
	exec("service mysql stop");
}

print("MySQL ROOT password reset...\n");
sleep(10);
system("mariadbd-safe --skip-grant-tables --init-file={$tpath}/reset-mysql-password.sql >/dev/null 2>&1 &");
sleep(15);
system("mysqladmin -u root -p='{$pass}' shutdown");
print("Start MySQL service...\n");
if (isServiceExists('mariadb')) {
	exec("service mariadb start");
} elseif(isServiceExists('mysqld')) {
	exec("service mysqld start");
} else {
	exec("service mysql start");
}

exec("'rm' -f {$tpath}/reset-mysql-password.sql");

$conn = new mysqli('localhost', 'root', $pass, 'mysql');

if ($conn->connect_errno) {
	printf("Connect failed: %s\n", $conn->connect_error);

	exit();
}

$conn->close();

$a['mysql']['dbpassword'] = $pass;

slave_save_db("dbadmin", $a);