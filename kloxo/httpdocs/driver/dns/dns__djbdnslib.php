<?php

include_once("dns__lib.php");

class dns__djbdns extends dns__
{
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

		$initfile = getLinkCustomfile("/home/djbdns/etc/init.d", "djbdns.init");

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
		parent::createConfFileTrue($action);
	}

	function syncCreateConf($action = null)
	{
		parent::syncCreateConfTrue($action);
	}

	function createAllowTransferIps()
	{
		parent::createAllowTransferIpsTrue();
	}

	function dbactionAdd()
	{
		parent::dbactionAddTrue();
	}

	function dbactionUpdate($subaction)
	{
		parent::dbactionUpdateTrue($subaction);
	}

	function dbactionDelete()
	{
		parent::dbactionDeleteTrue();
	}

	function dosyncToSystemPost()
	{
		 // MR -- no action here
	}
}