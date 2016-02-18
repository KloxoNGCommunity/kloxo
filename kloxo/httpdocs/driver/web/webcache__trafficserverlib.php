<?php

include_once("webcache__lib.php");

class webcache__trafficserver extends webcache__
{
	function __construct()
	{
		parent::__construct();
	}

	static function uninstallMe()
	{
		parent::uninstallMeTrue('trafficserver');
	}

	static function installMe()
	{
		parent::installMeTrue('trafficserver');
		self::copyConfigMe();
	}

	function copyConfigMe()
	{
		$nolog = null;

		$pathsrc = "../file/trafficserver";
		$pathdrv = "/opt/configs/trafficserver";
		$pathetc = "/etc";

		log_cleanup("Copy all contents of 'trafficserver' (from '{$pathsrc}')", $nolog);

		log_cleanup("- Copy to {$pathdrv}", $nolog);
		exec("'cp' -rf {$pathsrc} /opt/configs");

	//	if (!file_exists("/etc/trafficserver")) { return; }

		$a = array("records.config", "remap.config", "storage.config", "ip_allow.config");

		foreach ($a as $k => $v) {
			$t = getLinkCustomfile($pathdrv . "/etc/conf", $v);
			lxfile_cp($t, "$pathetc/trafficserver/{$v}");
		}
	}

}