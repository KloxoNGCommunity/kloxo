<?php 

// by mustafa@bigraf.com for Kloxo-MR

include_once "htmllib/lib/include.php"; 

$mysqlbranch = getRpmBranchInstalled('mysql');

echo "*** Change MariaDB to MySQL - begin ***\n";

echo "- Fix Service List\n";
exec("sh /script/fix-service-list >/dev/null 2>&1");

if (strpos($mysqlbranch, "mysql") !== false) {
	echo "- Already '{$mysqlbranch}' installed\n";
} elseif (strpos($mysqlbranch, "MariaDB") !== false) {

	exec("yum list|grep MariaDB", $out, $ret);
	
	if ($out) {
		echo "- Repo for MariaDB exists.\n";
		echo "  Open '/etc/yum.repos.d/kloxo-mr.repo and change 'enable=1' to 'enable=0'\n";
		echo "  under [kloxo-mr-mariadb32] for 32bit OS or [kloxo-mr-mariadb64] for 64bit OS\n";
	} else {
		exec("rpm -qa|grep {$mysqlbranch}", $out2, $ret);

		echo "- Remove MariaDB packages\n";
		foreach ($out2 as &$o) {
			exec("rpm -e {$o} --nodeps >/dev/null 2>&1");
		}

		echo "- Install MySQL\n";
		exec("yum install mysql mysql-server -y >/dev/null 2>&1");

		if (file_exists("/etc/my.cnf.d/my.cnf")) {
			exec("cp -f /etc/my.cnf.d/my.cnf /etc/my.cnf >/dev/null 2>&1");
		} elseif (file_exists("/etc/my.cnf._bck_")) {
			exec("cp -f /etc/my.cnf._bck_ /etc/my.cnf >/dev/null 2>&1");
		}

		echo "- Restart MySQL\n";
		exec("service mysqld restart");
	}
} else {
	echo "- No MySQL or MariaDB installed\n";
}

echo "\n";
echo " - Note: remove 'skip-innodb' from '/etc/my.cnf' and '/etc/my.cnf.d/my.cnf'.\n";
echo "   Need reboot!.\n\n";

echo "*** Change MariaDB to MySQL - end ***\n";



