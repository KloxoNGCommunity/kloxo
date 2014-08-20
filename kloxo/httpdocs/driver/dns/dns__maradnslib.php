<?php

include_once("dns__lib.php");

class dns__maradns extends dns__
{
	function __construct()
	{
		parent::__construct();
	}

	static function unInstallMe()
	{
		setRpmRemoved("maradns");

		if (file_exists("/etc/init.d/maradns")) {
			lunlink("/etc/init.d/maradns");
		}
	}

	static function installMe()
	{
		setRpmInstalled("maradns");

		$initfile = getLinkCustomfile("/opt/configs/maradns/etc/init.d", "maradns.init");

		if (file_exists($initfile)) {
			lxfile_cp($initfile, "/etc/init.d/maradns");
		}

		lxshell_return("chkconfig", "maradns", "on");

		createRestartFile("maradns");

		self::copyConfigMe();
	}

	static function copyConfigMe()
	{
		setCopyDnsConfFiles('maradns');
	}

	function createConfFile($action = null)
	{
		parent::createConfFileTrue('maradns', $action);
	}

	function syncCreateConf($action = null)
	{
		parent::syncCreateConfTrue('maradns', $action);
	}

	function createAllowTransferIps()
	{
		parent::createAllowTransferIpsTrue('maradns');
	}

	function dbactionAdd()
	{
		parent::dbactionAddTrue('maradns');
	}

	function dbactionUpdate($subaction)
	{
		parent::dbactionUpdateTrue('maradns', $subaction);
	}

	function dbactionDelete()
	{
		parent::dbactionDeleteTrue('maradns');
	}

	function dosyncToSystemPost()
	{
		 // MR -- no action here
	}
}