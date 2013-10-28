<?php

include_once("dns__lib.php");

class dns__bind extends dns__
{
	static function unInstallMe()
	{
		setRpmRemoved("bind");

		if (file_exists("/etc/init.d/named")) {
			lunlink("/etc/init.d/named");
		}

		setRpmInstalled("bind-utils");
	}

	static function installMe()
	{
		setRpmInstalled("bind");

		setRpmInstalled("bind-utils");
		setRpmRemoved("bind-chroot");

		$initfile = getLinkCustomfile("/home/bind/etc/init.d", "named.init");

		if (file_exists($initfile)) {
			lxfile_cp($initfile, "/etc/init.d/named");
		}

		lxshell_return("chkconfig", "named", "on");

		createRestartFile("named");

		self::copyConfigMe();
	}

	static function copyConfigMe()
	{
		$nolog = null;

		$pathsrc = "/usr/local/lxlabs/kloxo/file/bind";
		$pathdrv = "/home/bind";
		$pathetc = "/etc/";

		log_cleanup("Copy all contents of 'bind'", $nolog);

		log_cleanup("- Copy {$pathsrc} to {$pathdrv}", $nolog);
		exec("cp -rf {$pathsrc} /home");

		$t = getLinkCustomfile($pathdrv . "/etc/conf", "named.conf");

		log_cleanup("- Copy {$t} to {$pathetc}/named.conf", $nolog);
		lxfile_cp($t, "{$pathetc}/named.conf");

	}

	function createConfFile($action = null)
	{
		parent::createConfFileTrue('bind', $action);
	}

	function syncCreateConf($action = null)
	{
		parent::syncCreateConfTrue('bind', $action);
	}

	function createAllowTransferIps()
	{
		parent::createAllowTransferIpsTrue('bind');
	}

	function dbactionAdd()
	{
		parent::dbactionAddTrue('bind');
	}

	function dbactionUpdate($subaction)
	{
		parent::dbactionUpdateTrue('bind', $subaction);
	}

	function dbactionDelete()
	{
		parent::dbactionDeleteTrue('bind');
		exec("rndc reconfig");
	}

	function dosyncToSystemPost()
	{
		 // MR -- no action here
	}
}