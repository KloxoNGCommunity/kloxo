<?php 

include_once "lib/html/include.php";

// MR -- make trouble for reset password if serverfile dir not exists
$tpath = "/usr/local/lxlabs/kloxo/serverfile";
// $tpath = "/tmp";

if (isset($argv[1])) {
	$pass = $argv[1];
} else {
	$pass = randomString(9);
}

/*
$text = <<<EOF
UPDATE mysql.user SET Password=PASSWORD('PASSWORD') WHERE User='USER';
UPDATE mysql.user SET authentication_string=PASSWORD('PASSWORD') WHERE User='USER';
ALTER USER 'USER'@'locxalhost' IDENTIFIED BY 'PASSWORD';
FLUSH PRIVILEGES;
EOF;
*/

$text = <<<EOF
USE mysql;
UPDATE mysql.user SET Password=PASSWORD('PASSWORD') WHERE User='USER';
FLUSH PRIVILEGES;
EOF;

$text = str_replace("'USER'", "'root'", $text);
$text = str_replace("'PASSWORD'", "'{$pass}'", $text);

file_put_contents("{$tpath}/reset-mysql-password.sql", $text);

print("Stop MySQL service...\n");
if (isServiceExists('mysqld')) {
//	exec("service mysqld stop");
	exec("service mysqld stop; pkill mysqld; pkill mysqld_safe");
} else {
//	exec("service mysql stop");
	exec("service mysql stop; pkill mysqld; pkill mysqld_safe");
}

print("MySQL ROOT password reset...\n");
//sleep(5);
exec("mysqld_safe --defaults-file=/etc/my.cnf --init-file={$tpath}/reset-mysql-password.sql &");
//sleep(5);

print("Start MySQL service...\n");
if (isServiceExists('mysqld')) {
	exec("pkill mysqld; pkill mysqld_safe; service mysqld start");
} else {
	exec("pkill mysqld; pkill mysqld_safe; service mysql start");
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

exec("mysql -u root -p{$pass} kloxo -e \"UPDATE dbadmin SET dbpassword='{$pass}' WHERE dbadmin_name='root';\"");