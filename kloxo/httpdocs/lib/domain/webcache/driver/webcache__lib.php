<?php

class webcache__ extends lxDriverClass
{
	function __construct()
	{
	}

	static function uninstallMeTrue($drivertype = null)
	{
		lxshell_return("service", $drivertype, "stop");

		lxshell_return("chkconfig", $drivertype, "off");

		setRpmRemovedViaYum($drivertype);

		if (file_exists("/etc/init.d/{$drivertype}")) {
			lunlink("/etc/init.d/{$drivertype}");
		}
	}

	static function installMeTrue($drivertype = null)
	{
		setCopyWebCacheConfFiles($drivertype);

		setRpmInstalled($drivertype);

		lxshell_return("chkconfig", $drivertype, "on");

		createRestartFile($drivertype);
	}
}
