<?php 

// by mustafa@bigraf.com for Kloxo-MR

include_once "htmllib/lib/include.php"; 

$mysqlbranch = getRpmBranchInstalled('mysql');

echo "*** Change MySQL to MariaDB - end ***\n";

echo "- Fix Service List\n";
exec("sh /script/fix-service-list >/dev/null 2>&1");

if (strpos($mysqlbranch, "MariaDB") !== false) {
	echo "- Already '{$mysqlbranch}' installed\n";
} elseif (strpos($mysqlbranch, "mysql") !== false) {

	exec("yum list|grep MariaDB", $out, $ret);
	
	if (!$out) {
		echo "- No repo for MariaDB.\n";
		echo "  Open '/etc/yum.repos.d/kloxo-mr.repo and change 'enable=0' to 'enable=1'\n";
		echo "  under [kloxo-mr-mariadb32] for 32bit OS or [kloxo-mr-mariadb64] for 64bit OS\n";
	} else {
		
		exec("rpm -qa|grep {$mysqlbranch}", $out, $ret);

		exec("cp -f /etc/my.cnf /etc/my.cnf._bck_ >/dev/null 2>&1");

		echo "- Remove MySQL packages\n";
		foreach ($out as &$o) {
			if (strpos($o, "-mysql") !== false) { continue; }
			if (strpos($o, "mysqlclient") !== false) { continue; }
			exec("rpm -e {$o} --nodeps >/dev/null 2>&1");
		}

		echo "- Install MariaDB\n";
		exec("yum install MariaDB-server MariaDB-client MariaDB-compat MariaDB-common -y >/dev/null 2>&1");

		exec("cp -f /etc/my.cnf._bck_ /etc/my.cnf.d/my.cnf >/dev/null 2>&1");

		echo "- Restart MariaDB\n";
		exec("service mysql restart");
	}
} else {
	echo "- No MySQL or MariaDB installed\n";
}

echo "\n - Note: remove 'skip-innodb' from '/etc/my.cnf' and '/etc/my.cnf.d/my.cnf'\n\n";

echo "*** Change MySQL to MariaDB - end ***\n";



