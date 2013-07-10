<?php 

// by mustafa@bigraf.com for Kloxo-MR

include_once "htmllib/lib/include.php"; 

// initProgram('admin');

$list = parse_opt($argv);

$engine = ($list['engine']) ? $list['engine'] : 'MyISAM';
$database = (isset($list['database'])) ? $list['database'] : null;
$table = (isset($list['table'])) ? $list['table'] : null;
$config = (isset($list['config'])) ? $list['config'] : null;

setMysqlConvert($engine, $database, $table, $config);

/* ****** BEGIN - setMysqlConvert ***** */

/* move from mysql-convert.php */

function setMysqlConvert($engine, $database, $table, $config)
{
	global $gbl, $sgbl, $login, $ghtml;

	log_cleanup("Convert of MySQL engine");

	$engine = strtolower($engine);

	$database = ($database) ? $database : '_all_';
	$table = ($table) ? $table : '_all_';
	$config = ($config) ? $config : 'yes';

	$pass = slave_get_db_pass();

	// MR -- need if switch mysql from 5.0.x to higher but mysql.servers not created
	log_cleanup("- Insert mysql.servers table if not exist");
	exec("mysql -f -u root -p{$pass} mysql < /usr/local/lxlabs/kloxo/file/mysql.servers.sql");

	$mysqlbranch = getRpmBranchInstalled('mysql');
	
	if (strpos($mysqlbranch, "MariaDB") !== false) {
		$mycnfpath = "/etc/my.cnf.d/my.cnf";
	} else {
		$mycnfpath = "/etc/my.cnf";
	}

	//--- the first - to 'disable' skip- and restart mysql
	system("sed -i 's/skip/\;###123###skip/g' {$mycnfpath}");

	restartMySql();

	mysql_connect('localhost', 'root', $pass);

	log_cleanup("- Converting to ".$engine." engine");

	if ($database === '_all_') {
		$dbs = mysql_query('SHOW databases');

		while ($db = mysql_fetch_array($dbs)) {
			log_cleanup("-- ".$db[0]." database converted");
		/*
			if ($db[0] === 'mysql') {
				log_cleanup("--- 'mysql' not converted");
			}
			else if ($db[0] === 'information_schema') {
				log_cleanup("--- 'information_schema' not converted");
			}
			else if ($db[0] === 'performance_schema') {
				log_cleanup("--- 'performance_schema not' converted");
			}
			else {
		*/
				mysql_select_db($db[0]);

				if ($table === '_all_') {
					$tbls = mysql_query('SHOW tables');

					while ($tbl = mysql_fetch_array($tbls)) {
						log_cleanup("--- '".$tbl[0]."' table converted");
						mysql_query("ALTER TABLE {$tbl[0]} ENGINE={$engine}");
					}
				}
				else {
					mysql_query("ALTER TABLE {$table} ENGINE={$engine}");
					log_cleanup("--- '".$table."' table converted");
				}
		//	}
		}
	}
	else {
		mysql_select_db($database);

		log_cleanup("-- '".$database."' database converted");

		if ($table === '_all_') {
			$tbls = mysql_query('SHOW tables');

			while ($tbl = mysql_fetch_array($tbls)) {
				log_cleanup("--- '".$tbl[0]."' table converted");
				mysql_query("ALTER TABLE {$tbl[0]} ENGINE={$engine}");
			}
		}
		else {
			mysql_query("ALTER TABLE {$table} ENGINE={$engine}");
			log_cleanup("--- '".$table."' table");
		}
	}

	//--- the second - back to 'original' config and restart mysql
	system("sed -i 's/\;###123###skip/skip/g' {$mycnfpath}");

	restartMySql();

	if ($config === 'yes') {
		if ($database === '_all_') {
			$string = file_get_contents($mycnfpath);

			$string_array = explode("\n", $string);

			$string_collect = null;

			foreach($string_array as $sa) {
				if (stristr($sa, 'skip-innodb') !== FALSE) {
					$string_collect .= "";
					continue;
				}
				if (stristr($sa, 'default-storage-engine') !== FALSE) {
					$string_collect .= "";
					continue;
				}
				$string_collect .= $sa."\n";
			}
		
			if ($engine !== 'innodb') {
				$string_source = "[mysqld]\n";
				$string_replace = "[mysqld]\nskip-innodb\ndefault-storage-engine={$engine}\n";
				log_cleanup("- Added \"skip-innodb and default-storage-engine=".$engine."\" in {$mycnfpath}");
			}
			else {
				$string_source = "[mysqld]\n";
				$string_replace = "[mysqld]\ndefault-storage-engine={$engine}\n";
				log_cleanup("- Added \"default-storage-engine=".$engine."\" in {$mycnfpath}");
			}

			$string_collect = str_replace($string_source, $string_replace, $string_collect);

			file_put_contents($mycnfpath, $string_collect);
		}
	}

	log_cleanup("- Convert to '{$engine}' engine finished");

	log_cleanup("- MySQL Service restarted");
	restartMySql();
}

function restartMySql() {
	if (file_exists("/etc/rc.d/init.d/mysqld")) {
		$ret = lxshell_return("service", "mysqld", "restart");
		if ($ret) { throw new lxexception('mysqld_restart_failed', 'parent'); }
	} elseif (file_exists("/etc/rc.d/init.d/mysql")) {
		$ret = lxshell_return("service", "mysql", "restart");
		if ($ret) { throw new lxexception('mysql_restart_failed', 'parent'); }
	} else {
		echo("No service of mysql/mysqld... exit!");
	//	exit;
	}
}

/* ****** END - setMysqlConvert ***** */