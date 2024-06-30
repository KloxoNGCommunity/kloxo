<?php 
include_once "lib/html/include.php";
include_once "lib/html/updatelib.php";

exit_if_another_instance_running();
debug_for_backend();
updatecleanup_main();

function updatecleanup_main()
{
	global $argc, $argv;
	global $gbl, $sgbl, $login, $ghtml; 

	$program = $sgbl->__var_program_name;
	$opt = parse_opt($argv);

	if ($opt['type'] === 'master') {
		initProgram('admin');
		$flg = $sgbl->__path_program_start_vps_flag;

		if (!lxfile_exists($flg)) {
			set_login_skin_to_simplicity();
		}
	} else {
		$login = new Client(null, null, 'update');
	}

	log_cleanup("*** Execute Update (cleanup) - BEGIN ***");

	if ($opt['type'] === 'master') {
		$sgbl->slave = false;
		
		if (!is_secondary_master()) {
			print(">>> Execute fixDataBaseIssues() <<<\n");
			fixDataBaseIssues();
			print(">>> Execute doUpdates() <<<\n");
			doUpdates();
			print(">>> Execute driverload.php <<<\n");
			lxshell_return($sgbl->__path_php_path, "../bin/common/driverload.php");
		}

		print(">>> Execute update_all_slave() <<<\n");
		update_all_slave();
		print(">>> Execute cp_dbfile() <<<\n");
		cp_dbfile();
	} else {
		$sgbl->slave = true;
	}

	if (!is_secondary_master()) {
		print(">>> Execute updatecleanup() <<<\n");
		updatecleanup();
	}

	if ($opt['type'] === 'master') {
		lxfile_touch($sgbl->__path_program_start_vps_flag);
	}

	// MR -- mysql not start after kloxo slave install
	log_cleanup("Prepare MySQL/MariaDB service");

	if (isServiceExists('mariadb')) {
		log_cleanup("- MariaDB activated")
		exec("chkconfig mysql off >/dev/null 2>&1");
		exec("chkconfig mysqld off >/dev/null 2>&1");
		exec("chkconfig mariadb on >/dev/null 2>&1");
	
	} elseif (isServiceExists('mysqld')) {
		log_cleanup("- MySQL activated");
		exec("chkconfig mysql off >/dev/null 2>&1");
		exec("chkconfig mysqld on >/dev/null 2>&1");
	
	} else {
		log_cleanup("- MariaDB activated");
		exec("chkconfig mysqld off >/dev/null 2>&1");
		exec("chkconfig mysql on >/dev/null 2>&1");
	}

	log_cleanup("- Updating Main services");

	## MR -- change to update all

	log_cleanup('Update All packages');
	log_cleanup('- WAIT to process...');
//	exec("'rm' -f /var/run/yum.pid");
	lxshell_return("yum", "clean", "expire-cache");
	$ret = lxshell_return("yum", "update", "-y");

	if (!$ret) {
		log_cleanup("- No update found/not installed");
	} else {
		log_cleanup("- New version of packages installed");
	}

	if (isset($opt['without-services'])) {
		// no action
	} else {
		setInitialServices();

		log_cleanup("Fix services");
		setUpdateConfigWithVersionCheck(getListOnList('fix'), $opt['type']);
	}

	// MR -- installatron need ownership as root:root
	if (is_link("/usr/local/lxlabs/kloxo/httpdocs/installatron")) {
		unlink("/usr/local/lxlabs/kloxo/httpdocs/installatron");
		symlink("/var/installatron/frontend", "/usr/local/lxlabs/kloxo/httpdocs/installatron");
	}

	log_cleanup("- Fix Hiawatha service");
	fix_hiawatha();

	log_cleanup("*** Execute Update (cleanup) - END ***");
}

function cp_dbfile()
{
	global $gbl, $sgbl, $login, $ghtml;

	$progname = $sgbl->__var_program_name;

	lxfile_cp("../sbin/{$progname}db", "/usr/bin/{$progname}db");
	lxfile_generic_chmod("/usr/bin/{$progname}db", "0755");
}

