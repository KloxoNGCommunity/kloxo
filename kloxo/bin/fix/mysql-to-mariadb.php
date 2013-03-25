<?php 

// by mustafa@bigraf.com for Kloxo-MR

include_once "htmllib/lib/include.php"; 

$mysqlbranch = getRpmBranchInstalled('mysql');

echo "*** Change MySQL to MariaDB - begin ***\n";

system("yum clean all");
system("sh /script/fix-service-list");
echo "\n";

if (strpos($mysqlbranch, "MariaDB") !== false) {
	echo "* Already '{$mysqlbranch}' installed\n";
} elseif (strpos($mysqlbranch, "mysql") !== false) {

	exec("yum list|grep MariaDB", $out, $ret);
	
	if ($ret) {
		echo "- No repo for MariaDB.\n";
		echo "  Open '/etc/yum.repos.d/kloxo-mr.repo and change 'enable=0' to 'enable=1'\n";
		echo "  under [kloxo-mr-mariadb32] for 32bit OS or [kloxo-mr-mariadb64] for 64bit OS\n";
	} else {
		
		$out2 = shell_exec("rpm -qa|grep {$mysqlbranch}");

		$arr = explode("\n", $out2);

		echo "- Remove MySQL packages\n";
		exec("cp -f /etc/my.cnf /etc/my.cnf._bck_");
		
		foreach ($arr as &$o) {
			if (strpos($o, "-mysql") !== false) { continue; }
			if (strpos($o, "mysqlclient") !== false) { continue; }
			system("rpm -e {$o} --nodeps");
		}

		echo "- Install MariaDB\n";
		system("yum install MariaDB-server MariaDB-client MariaDB-compat MariaDB-common -y");

		system("cp -f /etc/my.cnf._bck_ /etc/my.cnf.d/my.cnf");

		echo "- Restart MariaDB\n";
		system("chkconfig mysql on");
		system("service mysql restart");
	}
} else {
	echo "- No MySQL or MariaDB installed\n";
}

echo "\n";
echo " - Note: remove 'skip-innodb' from '/etc/my.cnf' and '/etc/my.cnf.d/my.cnf'.\n";
echo "   Need reboot!.\n\n";

echo "*** Change MySQL to MariaDB - end ***\n";



