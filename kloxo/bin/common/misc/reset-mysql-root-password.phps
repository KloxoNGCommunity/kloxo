<?php 

include_once "htmllib/lib/include.php";

if (isset($argv[1])) {
	$pass = $argv[1];
} else {
	$pass = "";
}

$text = <<<EOF
UPDATE mysql.user SET Password=PASSWORD('PASSWORD') WHERE User='USER';
FLUSH PRIVILEGES;
EOF;

$text = str_replace("'USER'", "'root'", $text);
$text = str_replace("'PASSWORD'", "'{$pass}'", $text);

file_put_contents("/tmp/reset-mysql-password.sql", $text);

print("Stop MySQL service...\n");
if (file_exists("/etc/init.d/mysql")) {
	exec("service mysql stop");
} else {
	exec("service mysqld stop");
}

print("MySQL ROOT password reset...\n");
sleep(10);
system("mysqld_safe --init-file=/tmp/reset-mysql-password.sql >/dev/null 2>&1 &");
sleep(15);

print("Start MySQL service...\n");
if (file_exists("/etc/init.d/mysql")) {
	exec("service mysql start");
} else {
	exec("service mysqld start");
}

exec("rm -f /tmp/reset-mysql-password.sql");

$conn = mysqli_connect('localhost', 'root', $pass, 'mysql');

if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

mysqli_close($conn);

$a['mysql']['dbpassword'] = $pass;

slave_save_db("dbadmin", $a);