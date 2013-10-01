<?php

class webcache__ extends lxDriverClass
{
	function __construct()
	{
	}

	static function uninstallMeTrue($drivertype = null)
	{
		if ($drivertype === 'none') { return; }

		lxshell_return("service", $drivertype, "stop");

		lxshell_return("chkconfig", $drivertype, "off");

		setRpmRemovedViaYum($drivertype);

		if (file_exists("/etc/init.d/{$drivertype}")) {
			lunlink("/etc/init.d/{$drivertype}");
		}
	}

	static function installMeTrue($drivertype = null)
	{
		if ($drivertype === 'none') { return; }

		setRpmInstalled($drivertype);

		setCopyWebCacheConfFiles($drivertype);

		lxshell_return("chkconfig", $drivertype, "on");

		createRestartFile($drivertype);
	}
}
