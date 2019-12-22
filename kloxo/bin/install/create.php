<?php 
include_once "lib/html/include.php";
include_once "lib/html/initlib.php";
include_once "../bin/install/sql.php";
include_once "../bin/install/init.php";

create_main();

function create_main()
{
	global $argc, $argv;
	global $gbl, $sgbl, $login, $ghtml;

	$opt = parse_opt($argv);

	lxfile_mkdir("{$sgbl->__path_program_etc}/conf");
	lxfile_mkdir("{$sgbl->__path_program_root}/pid");
	lxfile_mkdir("{$sgbl->__path_program_root}/log");
	lxfile_mkdir("{$sgbl->__path_httpd_root}");

	print(">>> Execute os_fix_lxlabs_permission() <<<\n");
	os_fix_lxlabs_permission();
	print(">>> Execute os_create_program_service() <<<\n");
	os_create_program_service();
	print(">>> Execute os_create_kloxo_service_once() <<<\n");
	os_create_kloxo_service_once();

	if (isset($opt['admin-password'])) {
		$admin_pass = $opt['admin-password'];
	} else {
		$admin_pass = 'admin';
	}

	if ($opt['install-type'] == 'master') {
		print(">>> Execute create_mysql_db() for MASTER <<<\n");
		create_mysql_db('master', $opt, $admin_pass);
		print(">>> Execute create_database() for MASTER <<<\n");
		create_database();
		print(">>> Execute create_general() for MASTER <<<\n");
		create_general();
		print(">>> Execute init_main() for MASTER <<<\n");
		init_main($admin_pass);
		print(">>> Execute collectquota.php... for MASTER <<<\n");
		lxshell_return("$sgbl->__path_php_path", "../bin/collectquota.php");
		print(">>> Execute tmpupdatecleanup.php for MASTER... <<<\n");
		print("- This will take a long time... Please wait...\n");
		system("lxphp.exe ../bin/common/tmpupdatecleanup.php --type=master");
	} else if ($opt['install-type'] == 'slave') {
		init_slave($admin_pass);
		print(">>> Execute tmpupdatecleanup.php for SLAVE... <<<\n");
		print("- This will take a long time... Please wait...\n");
		system("lxphp.exe ../bin/common/tmpupdatecleanup.php --type=slave");
	} else if ($opt['install-type'] == 'supernode'){
		$sgbl->__path_sql_file = $sgbl->__path_sql_file_supernode;
		$sgbl->__var_dbf = $sgbl->__path_supernode_db;
		$sgbl->__path_admin_pass = $sgbl->__path_super_pass;
		$sgbl->__var_admin_user = $sgbl->__var_super_user;
		print(">>> Execute create_mysql_db() for SUPERNODE <<<\n");
		create_mysql_db('super', $opt, $admin_pass);
		init_supernode($admin_pass);
		print("\n");
	} else {
		print("Unknown Install type\n");
	//	flush();
	}

	print(">>> Create slavedb_driver <<<\n");
	os_create_default_slave_driver_db();
//	print(">>> Execute os_fix_some_permissions()... <<<\n");
//	os_fix_some_permissions();
}
