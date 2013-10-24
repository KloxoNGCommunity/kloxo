<?php

include_once("dns__lib.php");

class dns__nsd extends dns__
{
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

		$initfile = getLinkCustomfile("/home/nsd/etc/init.d", "nsd.init");

		if (file_exists($initfile)) {
			lxfile_cp($initfile, "/etc/init.d/nsd");
		}

		$path = "/home/nsd/conf/defaults";

		if (!file_exists("{$path}/nsd.master.conf") {
			touch("{$path}/nsd.master.conf");
			
		}

		if (!file_exists("{$path}/nsd.slave.conf") {
			touch("{$path}/nsd.slave.conf");
			
		}

		lxshell_return("chkconfig", "nsd", "on");

		createRestartFile("nsd");

		self::copyConfigMe();
	}

	static function copyConfigMe()
	{
		$nolog = null;

		$pathsrc = "/usr/local/lxlabs/kloxo/file/nsd";
		$pathdrv = "/home/nsd";
		$pathetc = "/etc/";

		log_cleanup("Copy all contents of 'nsd'", $nolog);

		log_cleanup("- Copy {$pathsrc} to {$pathdrv}", $nolog);
		exec("cp -rf {$pathsrc} /home");

		$pathtarget = "{$pathetc}/nsd";

		exec("mkdir -p {$pathtarget}");

		$t = getLinkCustomfile($pathdrv . "/etc/conf", "nsd.conf");

		log_cleanup("- Copy {$t} to {$pathtarget}/nsd.conf", $nolog);
		lxfile_cp($t, "{$pathtarget}/nsd.conf");

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
		exec("nsdc rebuild");
	}

	function dosyncToSystemPost()
	{
		 // MR -- no action here
	}
}