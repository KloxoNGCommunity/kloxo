<?php

include_once("dns__lib.php");

class dns__nsd extends dns__
{
	function __construct()
	{
		parent::__construct();
	}

	static function unInstallMe()
	{
		setRpmRemoved("nsd");

		if (file_exists("/etc/init.d/nsd")) {
			lunlink("/etc/init.d/nsd");
		}
	}

	static function installMe()
	{
		setRpmInstalled("nsd");

		$initfile = getLinkCustomfile("/opt/configs/nsd/etc/init.d", "nsd.init");

		if (file_exists($initfile)) {
			lxfile_cp($initfile, "/etc/init.d/nsd");
		}

		$path = "/opt/configs/nsd/conf/defaults";

		if (!file_exists("{$path}/nsd.master.conf")) {
			touch("{$path}/nsd.master.conf");
			
		}

		if (!file_exists("{$path}/nsd.slave.conf")) {
			touch("{$path}/nsd.slave.conf");
			
		}

		lxshell_return("chkconfig", "nsd", "on");

		createRestartFile("nsd");

		self::copyConfigMe();
	}

	static function copyConfigMe()
	{
		setCopyDnsConfFiles('nsd');
	}

	function createConfFile($action = null)
	{
		parent::createConfFileTrue('nsd', $action);
	}

	function syncCreateConf($action = null)
	{
		parent::syncCreateConfTrue('nsd', $action);
	}

	function createAllowTransferIps()
	{
		parent::createAllowTransferIpsTrue('nsd');
	}

	function dbactionAdd()
	{
		parent::dbactionAddTrue('nsd');
	}

	function dbactionUpdate($subaction)
	{
		parent::dbactionUpdateTrue('nsd', $subaction);
	}

	function dbactionDelete()
	{
		parent::dbactionDeleteTrue();
		exec("nsdc rebuild");
	}

	function dosyncToSystemPost()
	{
		 // MR -- no action here
	}
}