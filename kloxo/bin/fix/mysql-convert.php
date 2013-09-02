<?php

// release on Kloxo 6.1.7
// by mustafa.ramadhan@lxcenter.org

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

	//--- the first - to 'disable' skip- and restart mysql
	system("sed -i 's/skip/\;###123###skip/g' /etc/my.cnf");

	if (file_exists("/etc/init.d/mysql")) {
		exec("service mysql restart");
	} else {
		exec("service mysqld restart");
	}

	$conn = mysqli_connect('localhost', 'root', $pass);

	mysqli_select_db($conn, 'mysql');

	log_cleanup("- Converting to " . $engine . " engine");

	if ($database === '_all_') {
		try {
			$dbs = mysqli_query($conn, 'SHOW databases');

			while ($db = mysqli_fetch_array($dbs, MYSQLI_NUM)) {
				log_cleanup("-- " . $db[0] . " database converted");

				mysqli_select_db($conn, $db[0]);

				if ($table === '_all_') {
					try {
						$tbls = mysqli_query($conn, 'SHOW tables');
					} catch (Exception $e) {
						echo 'Message: ' . $e->getMessage();
					}

					while ($tbl = mysqli_fetch_array($tbls, MYSQLI_NUM)) {
						log_cleanup("--- " . $tbl[0] . " table converted");

						if (!$tbl[0]) {
							print("convert_{$tbl[0]}_table_failed");
						}

						mysqli_query($conn, "ALTER TABLE {$tbl[0]} ENGINE={$engine}");
					}
				} else {
					log_cleanup("--- " . $table . " table converted");

					try {
						mysqli_query($conn, "ALTER TABLE {$table} ENGINE ={$engine}");
					} catch (Exception $e) {
						echo 'Message: ' . $e->getMessage();
					}
				}
			}
		} catch (Exception $e) {
			echo 'Message: ' . $e->getMessage();
		}

	} else {
		mysqli_select_db($conn, $database);

		log_cleanup("-- " . $database . " database converted");

		if ($table === '_all_') {
			try {
				$tbls = mysqli_query($conn, 'show tables');

				while ($tbl = mysqli_fetch_array($tbls, MYSQLI_NUM)) {
					log_cleanup("--- " . $tbl[0] . " table converted");

					mysqli_query($conn, "ALTER TABLE {$tbl[0]} ENGINE={$engine}");
				}
			} catch (Exception $e) {
				echo 'Message: ' . $e->getMessage();
			}
		} else {
			log_cleanup("--- " . $table . " table");

			try {
				mysqli_query($conn, "ALTER TABLE {$table} ENGINE={$engine}");
			} catch (Exception $e) {
				echo 'Message: ' . $e->getMessage();
			}
		}
	}

	//--- the second - back to 'original' config and restart mysql
	system("sed -i 's/\;###123###skip/skip/g' /etc/my.cnf");

	if (file_exists("/etc/init.d/mysql")) {
		exec("service mysql restart");
	} else {
		exec("service mysqld restart");
	}

	if ($config === 'yes') {
		if ($database === '_all_') {
			if (file_exists("/etc/my.cnf.d/my.cnf")) {
				$mycnf = "/etc/my.cnf.d/my.cnf";
			} else {
				$mycnf = "/etc/my.cnf";
			}

			$string = file_get_contents($mycnf);

			$string_array = explode("\n", $string);

			$string_collect = null;

			foreach ($string_array as $sa) {
				if (stristr($sa, 'skip-innodb') !== FALSE) {
					$string_collect .= "";
					continue;
				}

				if (stristr($sa, 'default-storage-engine') !== FALSE) {
					$string_collect .= "";
					continue;
				}

				$string_collect .= $sa . "\n";
			}

			if ($engine === 'myisam') {
				$string_source = "[mysqld]\n";
				$string_replace = "[mysqld]\nskip-innodb\ndefault-storage-engine={$engine}\n";
				log_cleanup(" - Added 'skip-innodb' and 'default-storage-engine={$engine}' in '{$mycnf}'");
			} else {
				$string_source = "[mysqld]\n";
				$string_replace = "[mysqld]\n#skip-innodb\ndefault-storage-engine={$engine}\n";
				log_cleanup("- Added 'default-storage-engine={$engine}' in '{$mycnf}'");
			}

			$string_collect = str_replace($string_source, $string_replace, $string_collect);

			file_put_contents($mycnf, $string_collect);
		}
	}

	log_cleanup("- Convert of MySQL to '{$engine}' engine finished");

	log_cleanup("- MySQL Service restarted");

	if (file_exists("/etc/init.d/mysql")) {
		exec("service mysql restart");
	} else {
		exec("service mysqld restart");
	}
}

/* ****** END - setMysqlConvert ***** */
