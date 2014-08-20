<?php

include_once("dns__lib.php");

class dns__pdns extends dns__
{
	function __construct()
	{
		parent::__construct();
	}

	static function unInstallMe()
	{
		setRpmRemoved("pdns");

		if (file_exists("/etc/init.d/pdns")) {
			lunlink("/etc/init.d/pdns");
		}
	}

	static function installMe()
	{
		setRpmInstalled("pdns");

		$initfile = getLinkCustomfile("/opt/configs/pdns/etc/init.d", "pdns.init");

		if (file_exists($initfile)) {
			lxfile_cp($initfile, "/etc/init.d/pdns");
		}

		lxshell_return("chkconfig", "pdns", "on");

		createRestartFile("pdns");

		self::copyConfigMe();
	}

	static function copyConfigMe()
	{
		setCopyDnsConfFiles('pdns');
	}

	function createConfFile($action = null)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$this->syncAddFile($this->main->nname, $action);
	
		foreach ((array)$this->main->__var_addonlist as $d) {
			$this->syncAddFile($d->nname, $action);
		}
	}

	function syncAddFile($domainname, $action = null)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$input = array();

		$input['domainname'] = $domainname;
		$input['ttl'] = $this->main->ttl;
		$input['nameduser'] = $sgbl->__var_programuser_dns;
		$input['soanameserver'] = $this->main->soanameserver;
		$input['email'] = $this->main->__var_email;
		$input['serial'] = $this->main->__var_ddate;
		$input['dns_records'] = $this->main->dns_record_a;

		// MR -- not work and not implementing yet!
	//	$input['account'] = $this->parent->getRealClientParentO()->getPathFromName();

		if ($action) {
			$input['action'] = $action;
		}

		if (!isset($input['action'])) {
			$input['action'] = 'add';
		}

		self::setSpecific($input);
	}

	function dbactionAdd()
	{
		global $gbl, $sgbl, $login, $ghtml;

		$this->createConfFile();
	}

	function dbactionUpdate($subaction)
	{
		$this->createConfFile('update');
	}

	function dbactionDelete()
	{
		$input = array();

		$input['domainname'] = $domainname;

		$input['action'] = 'delete';

		self::setSpecific($input);

		foreach ((array)$this->main->__var_addonlist as $d) {
			$input = array();

			$input['domainname'] = $d->nname;

			$input['action'] = 'delete';

			$nameserver = null;

			foreach($this->main->dns_record_a as $dns) {
				if ($dns->ttype === "ns") {
					if (!$nameserver) {
						$nameserver = $dns->param;
					}
				}
			}

			if ($this->main->soanameserver) {
				$nameserver = $this->main->soanameserver;
			}

			$input['nameserver'] = $nameserver;

			self::setSpecific($input);
		}
	}

	function dosyncToSystemPost()
	{
		 // MR -- no action here
	}

	static function setSpecific($input)
	{
		$input['rootpass'] = slave_get_db_pass();

		$tplsource = getLinkCustomfile("/opt/configs/pdns/tpl", "domains.conf.tpl");

		$tpl = file_get_contents($tplsource);

		$tplparse = getParseInlinePhp($tpl, $input);
	}
}