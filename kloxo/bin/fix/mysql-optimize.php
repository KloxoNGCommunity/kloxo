<?php 

// by mustafa@bigraf.com for Kloxo-MR

include_once "lib/html/include.php"; 

// initProgram('admin');

$list = parse_opt($argv);

$select = strtolower($list['select']);

$database = (isset($list['database'])) ? $list['database'] : null;

setMysqlOptimize($select, $database);

/* ****** BEGIN - setMysqlOptimize ***** */

function setMysqlOptimize($select, $database = null)
{
	global $gbl, $sgbl, $login, $ghtml;

	log_cleanup("Mysql Check/Repair/Optimize");

	$database = ($database) ? $database : "_all_";

	$pass = slave_get_db_pass();

	if ($select === 'check') {
		log_cleanup("- Checking database");

		if ($database === '_all_') {
			system("mysqlcheck --user=root --password=\"{$pass}\" --check --all-databases");
		}
		else {
			system("mysqlcheck --user=root --password=\"{$pass}\" --check --databases {$dbname}");
		}
	}

	if ($select === 'repair') {
		log_cleanup("- Repairing database");

		if ($database === '_all_') {
			system("mysqlcheck --user=root --password=\"{$pass}\" --repair --all-databases");
		}
		else {
			system("mysqlcheck --user=root --password=\"{$pass}\" --repair --databases {$dbname}");
		}
	}
	else if ($select === 'optimize') {
		log_cleanup("- Compacting database");

		if ($database === '_all_') {
			system("mysqlcheck --user=root --password=\"{$pass}\" --optimize --all-databases");
		}
		else {
			system("mysqlcheck --user=root --password=\"{$pass}\" --optimize --databases {$dbname}");
		}
	}

	log_cleanup("- MySQL Service restart");
	$ret = lxshell_return("service", "mysqld", "restart");
	if ($ret) { throw new lxexception('mysqld_restart_failed', 'parent'); }
}

/* ****** END - setMysqlOptimize ***** */
