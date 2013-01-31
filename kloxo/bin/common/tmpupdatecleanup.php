<?php 
include_once "htmllib/lib/include.php";
include_once "htmllib/lib/updatelib.php";

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
		$flg = "__path_program_start_vps_flag";
		if (!lxfile_exists($flg)) {
			set_login_skin_to_feather();
		}
	} else {
		$login = new Client(null, null, 'update');
	}

	log_cleanup("*** Executing Update (cleanup) - BEGIN ***");
/*
	$correct_perm = "0644";
	$check_perm = substr(decoct( fileperms("/usr/local/lxlabs/$program/httpdocs/thirdparty/phpMyAdmin/config.inc.php") ), 2);

	if ($check_perm != $correct_perm) {
		lxfile_unix_chmod("/usr/local/lxlabs/$program/httpdocs/thirdparty/phpMyAdmin/config.inc.php","0644");
	}

	if (lxfile_exists(".svn")) {
		log_cleanup("- SVN Found... Exiting");
		exit;
	}
*/
	if ($opt['type'] === 'master') {
		$sgbl->slave = false;
		if (!is_secondary_master()) {
			updateDatabaseProperly();
			fixDataBaseIssues();
			doUpdates();
			lxshell_return("__path_php_path", "../bin/common/driverload.php");
		}
		update_all_slave();
		cp_dbfile();
	} else {
		$sgbl->slave = true;
	}

	if (!is_secondary_master()) {
		updatecleanup();
	}

	if ($opt['type'] === 'master') {
		lxfile_touch("__path_program_start_vps_flag");
	}

	// MR -- mysql not start after kloxo slave install
	log_cleanup("Preparing MySQL service");

	log_cleanup("- MySQL activated");
	exec("chkconfig mysqld on");
	
	log_cleanup("- MySQL restarted");
	exec("service mysqld restart");
	
	$slist = array(
		"httpd* lighttpd* nginx*",
		"mod_* mysql* php* lx*",
		"bind* djbdns* pure-ftpd*",
		"*-toaster bogofilter",
		"kloxo-*.noarch"
	);

	setUpdateServices($slist);
	
	// MR -- use this trick for qmail non-daemontools based
	log_cleanup("Preparing some services again");
	
//	log_cleanup("- courier-imap enabled and restart queue");
//	exec("chkconfig courier-imap on");
//	createRestartFile("courier-imap");
	
	log_cleanup("- qmail enabled and restart queue");
	exec("chkconfig qmail on");
	createRestartFile("qmail");

	$fixapps = array("dns", "web", "php", "mail", "ftpuser");
	setUpdateConfigWithVersionCheck($fixapps, $opt['type']);

	log_cleanup("Fixing 'vpopmail' MySQL password");
	exec("sh /script/fixvpop");
	log_cleanup("- Fixing process");

	log_cleanup("Fixing Qmail assign");
	exec("sh /script/fix-qmail-assign");
	log_cleanup("- Fixing process");

	if (file_exists("/var/qmail/supervise/smtp/supervise/ok")) {
		log_cleanup("Restarting Qmail services");
		exec("qmailctl restart");
		log_cleanup("- Restarting process");
	}

	// --- for anticipate change xinetd listing
	exec("service xinetd restart");

	log_cleanup("*** Executing Update (cleanup) - END ***");
}

function cp_dbfile()
{
	global $gbl, $sgbl, $login, $ghtml;

	$progname = $sgbl->__var_program_name;

	lxfile_cp("../sbin/{$progname}db", "/usr/bin/{$progname}db");
	lxfile_generic_chmod("/usr/bin/{$progname}db", "0755");
}

