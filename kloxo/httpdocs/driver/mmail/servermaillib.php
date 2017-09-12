<?php

class mail_graylist_wlist_a extends lxaclass {

	static $__desc = array("", "",  "whitelist_ip");

	// Data
	static $__desc_nname = array("", "",  "whitelist_ip");

	static function createListAddForm($parent, $class)
	{
		return true;
	}

	static function createListAlist($object, $class)
	{
	//	$alist = servermail::createShowPropertyList($alist);
		
	//	return $alist['property'];

		$alist[] = 'a=updateform&sa=update';
		$alist[] = 'a=updateform&sa=spamdyke';
		$alist[] = "a=list&c=mail_graylist_wlist_a";

		return $alist;
	}
}

class ServerMail extends lxdb
{
	static $__desc = array("", "",  "server_wide_mail_configuration");
	static $__desc_queuelifetime = array("", "",  "queue_life_time");
	static $__desc_myname = array("", "",  "my_name");
	static $__desc_queuelifetime_v_604800 = array("", "",  "queue_life_time");
	static $__desc_concurrencyremote = array("", "",  "no_of_mail_send");
	static $__desc_enable_maps = array("f", "",  "enable_maps_protection");
	static $__desc_spamdyke_flag = array("f", "",  "enable_spamdyke");
	static $__desc_domainkey_flag = array("f", "",  "enable_domainkey");

	static $__desc_smtp_instance = array("", "",  "max_smtp_instances");
	static $__desc_smtp_relay = array("t", "",  "smtp_relay");

	static $__desc_send_limit = array("", "",  "send_limit");

	static $__desc_max_size = array("", "",  "max_mail_attachment_size");
//	static $__desc_additional_smtp_port = array("", "",  "additional_smtp_port");
	static $__desc_virus_scan_flag = array("f", "",  "enable_virus_scan");
	static $__acdesc_update_update = array("", "",  "server_mail_settings");
	static $__acdesc_update_spamdyke = array("", "",  "spamdyke");
	static $__desc_greet_delay = array("", "",  "greet_delay");
	static $__desc_max_rcpnts = array("", "",  "maximum_recipients");
	static $__desc_graylist_flag = array("f", "",  "enable_graylisting");
	static $__desc_graylist_min_secs = array("", "",  "graylist_min_secs");
	static $__desc_graylist_max_secs = array("", "",  "graylist_max_secs");
	static $__desc_reject_empty_rdns_flag = array("f", "",  "reject_empty_rdns");
	static $__desc_reject_ip_in_cc_rdns_flag = array("f", "",  "reject_ip_in_cc_rdns");
	static $__desc_reject_missing_sender_mx_flag = array("f", "",  "reject_missing_sender_mx");
	static $__desc_reject_unresolvable_rdns_flag = array("f", "",  "reject_unresolvable_rdns");
//	static $__desc_dns_blacklists = array("", "",  "dns_blacklists");
	static $__desc_dns_blacklists = array("t", "",  "dns_blacklists");
	static $__desc_alt_smtp_sdyke_flag = array("f","","alt_smtp_sdyke");

	static $__desc_default_dns_blacklists_flag = array("f", "",  "enable_default_dns_blacklists");

	static $__desc_blacklist_headers = array("f", "",  "blacklist_headers");
	static $__desc_default_blacklist_headers_flag = array("f", "",  "enable_default_blacklist_headers");

	function createExtraVariables()
	{
		global $gbl, $sgbl, $login, $ghtml;

		$this->setDefaultValue("greet_delay", "1");
		$this->setDefaultValue("max_rcpnts","30");
		$this->setDefaultValue("graylist_min_secs","300");
		$this->setDefaultValue("graylist_max_secs","1814400");
		$this->setDefaultValue("reject_empty_rdns_flag","1");
		$this->setDefaultValue("reject_ip_in_cc_rdns_flag","1");
		$this->setDefaultValue("reject_missing_sender_mx_flag","1");
		$this->setDefaultValue("reject_unresolvable_rdns_flag","1");
		$this->setDefaultValue("alt_smtp_sdyke_flag",1);
	}

	function createShowPropertyList(&$alist)
	{
		$alist['property'][] = 'a=updateform&sa=update';
		$alist['property'][] = 'a=updateform&sa=spamdyke';
		$alist['property'][] = "a=list&c=mail_graylist_wlist_a";

		return $alist;
	}

	function postUpdate($subaction = null)
	{
		// We need to write because reads everything from the database.
		$this->write();

		if ($subaction === 'update') {
			exec("sh /script/fixdomainkey");
		}
	}

	function updatespamdyke($param) 
	{
		$path = "../file/template";

		if ($param['default_dns_blacklists_flag'] === 'on') {
			unlink ("{$path}/current.spamdyke_rbl.txt");
			$param['dns_blacklists'] = file_get_contents("{$path}/spamdyke_rbl.txt");
		} else {
			$content = $param['dns_blacklists'];
			$file = "{$path}/current.spamdyke_rbl.txt";

			file_put_contents($file, $content);
		}

		if ($param['default_blacklist_headers_flag'] === 'on') {
			unlink ("{$path}/current.spamdyke_blacklist_headers.txt");
			$param['dns_blacklists'] = file_get_contents("{$path}/spamdyke_blacklist_headers.txt");
		} else {
			$content = $param['blacklist_headers'];
			$file = "{$path}/current.spamdyke_blacklist_headers.txt";
			file_put_contents($file, $content);
		}

		file_put_contents('/var/qmail/spamdyke/blacklist_headers', $content);
		exec('chown qmaild:qmail /var/qmail/spamdyke/blacklist_headers');

		return $param;
	}

	function updateform($subaction, $param)
	{
		switch($subaction) {
			case "update":
				$vlist['myname'] = null;
			//	$vlist['enable_maps'] = null;
				$vlist['spamdyke_flag'] = null;

			//	if (csa($this->getParentO()->osversion, " 5")) {
					$vlist['domainkey_flag'] = null;

					if (!$this->virus_scan_flag) {
						$this->virus_scan_flag = 'off';
					}

					$vlist['virus_scan_flag'] = null;

					if (!$this->max_size) {
						$this->max_size = "20971520";
					}

					$vlist['max_size'] = null;
			//	}

				$vlist['queuelifetime'] = null;

				if (!$this->smtp_instance) {
					$this->smtp_instance = lfile_get_contents("/var/qmail/control/concurrencyincoming");
				}

				$vlist['smtp_instance'] = null;

			//	$vlist['additional_smtp_port'] = null;
			//	$vlist['alt_smtp_sdyke_flag'] = null;

				if (!$this->smtp_relay) {
					$this->smtp_relay = lfile_get_contents("/var/qmail/control/smtproutes");
				}

				$vlist['smtp_relay'] = null;

				$sl_file = "/var/qmail/control/sendlimit";

				if (!$this->send_limit) {
					if (!file_exists($sl_file)) {
						$this->send_limit = 3000;
					} else {
						$this->send_limit = lfile_get_contents($sl_file);
					}
				}

				$vlist['send_limit'] = null;

			//	$this->postUpdate($subaction);

				break;
			case "spamdyke":
				$vlist['greet_delay'] = null;
				$vlist['max_rcpnts']= null;
				$vlist['graylist_flag'] = null;
				$vlist['graylist_min_secs'] = null;
				$vlist['graylist_max_secs'] = null;
				$vlist['reject_empty_rdns_flag'] = null;
				$vlist['reject_ip_in_cc_rdns_flag'] = null;
				$vlist['reject_missing_sender_mx_flag'] = null;
				$vlist['reject_unresolvable_rdns_flag'] = null;

				$path = "../file/template";

				if (file_exists("{$path}/current.spamdyke_rbl.txt")) {
					$file = "{$path}/current.spamdyke_rbl.txt";
				} else {
					$file = "{$path}/spamdyke_rbl.txt";
				}

				$vlist['dns_blacklists'] = array("t", lfile_get_contents($file));

				$vlist['default_dns_blacklists_flag'] = null;

				if (file_exists("{$path}/current.spamdyke_blacklist_headers.txt")) {
					$file = "{$path}/current.spamdyke_blacklist_headers.txt";
				} else {
					$file = "{$path}/spamdyke_blacklist_headers.txt";
				}

				$vlist['blacklist_headers'] = array("t", lfile_get_contents($file));

				$vlist['default_blacklist_headers_flag'] = null;

				break;
		}

		return $vlist;
	}

}
