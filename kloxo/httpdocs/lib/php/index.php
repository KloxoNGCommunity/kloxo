<?php

chdir("../../");
include_once "lib/html/displayinclude.php";
index_main();

function index_main()
{
	init_language();

	print_index();
}

function redirect_no_frames($url)
{
	global $gbl, $sgbl, $login, $ghtml; 

	if ($ghtml->iset("frm_nf")) {
		if ($ghtml->frm_nf) {
			setcookie("program-nf", "1");
			$ghtml->print_redirect($url);
		} else {
			setcookie("program-nf", "", time() - 345566);
			$ghtml->print_redirect("/");
		}

	} else {
		if (isset($_COOKIE['program-nf'])) {
			$ghtml->print_redirect($url);
		} else {
			$ghtml->print_redirect("/");
		}
	}
}

function ip_blocked($client)
{
	global $gbl, $sgbl, $ghtml; 

	return false;

	$bl = $gbl->getList("allowedip");

	foreach((array) $bl as $b) {
	//	Ipaddress::checkWhetherToBlock($_SERVER['REMOTE_ADDR']))
		if (check_ip_network($b->nname, $_SERVER['REMOTE_ADDR']))
			return true;
	}

	return false;
}

function checkAttempt()
{
	global $gbl, $sgbl, $login, $ghtml; 

	$match = 0;

	try  {
		$att = $gbl->getFromList("loginattempt", $ip);
		$att->count++;
		$att->dbaction = "update";
	} catch (Exception $e) {
		$att = new Loginattempt(null, null, $ip);
		$att->count = 1;
		$att->dbaction = "add";
		$gbl->addToList("loginattempt", $att);
	}

	if ($att->count >= 5) {
		$att->delete();
		$bl = new BlockedIp(null, null, $ip);
		$bl->dbaction = "add";

		try {
			$gbl->addToList("blockedip", $bl);
		} catch (Exception $e) {
			dprint("Blocked up already exists. This is weird\n");
		}

		$ghtml->print_redirect("/login/?frm_emessage=blocked");
	} else {
		$ghtml->print_redirect("/login/?frm_emessage=login_error");
	}

	$gbl->was();
}

function session_login()
{
	global $gbl, $sgbl, $login, $ghtml;

	session_start();

	$_SESSION['last_login_time'] = time();

	if (isset($_SESSION['num_login_fail'])) {
		if($_SESSION['num_login_fail'] == 5) {
			// MR -- need reduce 15 secs to sync js time remaining
			if((time() - $_SESSION['last_login_time']) < ((10*60)-15)) {
				// alert to user wait for 10 minutes afer
			//	$ghtml->print_redirect("/login/?frm_emessage=blocked");
			} else {
				// after 10 minutes
				$_SESSION['num_login_fail'] = 0;

				$ghtml->print_redirect("/login/");
			}
		} else {
			$_SESSION['num_login_fail'] ++;
		//	$_SESSION['last_login_time'] = time();
		}
	} else {
		$_SESSION['num_login_fail'] = 1;
	//	$_SESSION['last_login_time'] = time();

		$ghtml->print_redirect("/login/?frm_emessage=login_error");
	}
}

function print_index() 
{
	global $gbl, $sgbl, $ghtml, $login;
	global $g_language_mes;

	ob_start();

	print_time('index');
	$cgi_clientname = $ghtml->frm_clientname; 

	Htmllib::checkForScript($cgi_clientname);
	$cgi_class = $ghtml->frm_class;

	if (!$cgi_class) {
		$cgi_class = getClassFromName($cgi_clientname);
	}

	$cgi_password = $ghtml->frm_password;
	$cgi_forgotpwd = $ghtml->frm_forgotpwd; 
	$cgi_email = $ghtml->frm_email;
	$cgi_key = $ghtml->frm_login_key;

	$cgi_token = $ghtml->frm_token;
	session_start();
	$sess_token = $_SESSION['frm_token'];

	// MR -- use != instead !==  because compare numeric
	if (!file_exists('./lib/php/no_need_token')) {
		if ($cgi_token != $sess_token) {
			if ((!$cgi_token) || (!$sess_token) || ($cgi_token != $sess_token)) {
				print("<div align=\"center\">*** {$g_language_mes->__emessage['token_not_match']} ***</div>");
				session_destroy();
				exit;
			}
		}
	}

	if (!$cgi_password || !$cgi_clientname) {
		$ghtml->print_redirect("/login/?frm_emessage=login_error");
	}

	$cgi_classname = 'client';

	if ($cgi_class) {
		$cgi_classname = $cgi_class;
	}

	if ($cgi_clientname == "" || ($cgi_password == "" && $cgi_key == "")) { 
		$cgi_forgotpwd = $ghtml->frm_forgotpwd;
		return;
	} 

	$ip = $_SERVER['REMOTE_ADDR'];

	if (!check_login_success($cgi_classname, $cgi_clientname, $cgi_password, $cgi_key)) {
		return;
	}

	log_log("login_success", "Successful Login to $cgi_clientname from " .  $_SERVER['REMOTE_ADDR']);

	if (check_disable_admin($cgi_clientname)) {
		$ghtml->print_redirect("/login/?frm_emessage=login_error");
		exit;
	}

	if (get_login($cgi_classname, $cgi_clientname)){
		check_blocked_ip();
		do_login($cgi_classname, $cgi_clientname);
		$login->was();
		$ghtml->print_redirect("/");
	} else  {
		$ghtml->cgiset("frm_emessage", "login_error");
	}

	$cgi_forgotpwd = $ghtml->frm_forgotpwd;
}

function check_login_success($cgi_classname, $cgi_clientname, $cgi_password, $cgi_key)
{
	global $gbl, $sgbl, $login, $ghtml; 

	if ($cgi_password) {
		if (check_raw_password($cgi_classname, $cgi_clientname, $cgi_password)) {
			return true;
		} else {
			session_login();
			log_log("login_fail", "Failed Login attempt to $cgi_clientname from " .  $_SERVER['REMOTE_ADDR']);
			$ghtml->print_redirect("/login/?frm_emessage=login_error");
			return false; 
		}
	}

	return false;

	if ($cgi_key) {
		$list = lscandir_without_dot_or_underscore("../etc/publickey");
		openssl_private_encrypt("string", $encstring, $cgi_key);

		foreach($list as $k) {
			$publickey = lfile_get_contents("../etc/publickey/$k");
			openssl_public_decrypt($encstring, $rstring, $publickey);
			if ($rstring === 'string') {
				return true;
			}
		}

		$ghtml->print_redirect("/login/?frm_emessage=login_error_key");

		return false;
	} 

	return false;
}

function check_blocked_ip()
{
	global $gbl, $sgbl, $login, $ghtml; 

	if (!$login->isAllowed()) {
		$ip = $_SERVER['REMOTE_ADDR'];
		log_message("Denied Entry from Ip $ip for $login->nname");
		// MR -- disable 'delete' because not exists
	//	$gbl->c_session->delete();
	//	$gbl->c_session->was();
		$ghtml->print_redirect_self("/login/?frm_emessage=not_in_list_of_allowed_ip&frm_m_emessage_data=$ip");
	}

	if ($login->isBlocked()) {
		$ip = $_SERVER['REMOTE_ADDR'];
		// MR -- disable 'delete' because not exists
	//	$gbl->c_session->delete();
	//	$gbl->c_session->was();
		log_message("Denied Entry from Ip $ip for $login->nname");
		$ghtml->print_redirect_self("/login/?frm_emessage=in_the_list_of_blocked_ip&frm_m_emessage_data=$ip");
	}
}
