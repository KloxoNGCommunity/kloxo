<?php

include_once("webcache__lib.php");

class webcache__squid extends webcache__
{
	function __construct()
	{
		parent::__construct();
	}

	static function uninstallMe()
	{
		parent::uninstallMeTrue('squid');
	}

	static function installMe()
	{
		parent::installMeTrue('squid');
		self::copyConfigMe();
	}

	static function copyConfigMe()
	{
		$nolog = null;

		$pathsrc = "../file/squid";
		$pathdrv = "/opt/configs/squid";
		$pathetc = "/etc";

		log_cleanup("Copy all contents of 'squid' (from '{$pathsrc}')", $nolog);

		log_cleanup("- Copy to {$pathdrv}", $nolog);
		exec("'cp' -rf {$pathsrc} /opt/configs");

	//	if (!file_exists("/etc/squid")) { return; }

		if (file_exists("{$pathdrv}/etc/conf")) {
			exec("'rm' -rf {$pathdrv}/etc/conf");
		}

		$t = getLinkCustomfile("{$pathdrv}/etc/conf/etc/squid", "squid.conf");
		lxfile_cp($t, "$pathetc/squid/squid.conf");
	}
}