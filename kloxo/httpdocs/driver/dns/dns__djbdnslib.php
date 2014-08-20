<?php

include_once("dns__lib.php");

class dns__djbdns extends dns__
{
	function __construct()
	{
		parent::__construct();
	}

	static function unInstallMe()
	{
		setRpmRemoved("djbdns");

		if (file_exists("/etc/init.d/djbdns")) {
			lunlink("/etc/init.d/djbdns");
		}
	}

	static function installMe()
	{
		setRpmInstalled("djbdns");

		$initfile = getLinkCustomfile("/opt/configs/djbdns/etc/init.d", "djbdns.init");

		if (file_exists($initfile)) {
			lxfile_cp($initfile, "/etc/init.d/djbdns");
		}

		lxshell_return("chkconfig", "djbdns", "on");

		// MR -- need setup
		lxshell_return("/etc/init.d/djbdns", "setup");

		createRestartFile("djbdns");

		self::copyConfigMe();
	}

	static function copyConfigMe()
	{
		// MR -- no need action
	}

	function createConfFile($action = null)
	{
		parent::createConfFileTrue('djbdns', $action);
	}

	function syncCreateConf($action = null)
	{
		parent::syncCreateConfTrue('djbdns', $action);
	}

	function createAllowTransferIps()
	{
		parent::createAllowTransferIpsTrue('djbdns');
	}

	function dbactionAdd()
	{
		parent::dbactionAddTrue('djbdns');
	}

	function dbactionUpdate($subaction)
	{
		parent::dbactionUpdateTrue('djbdns', $subaction);
	}

	function dbactionDelete()
	{
		parent::dbactionDeleteTrue('djbdns');
	}

	function dosyncToSystemPost()
	{
		 // MR -- no action here
	}
}