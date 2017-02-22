<?php

class webcache__ extends lxDriverClass
{
	function __construct()
	{
	}

	static function uninstallMeTrue($drivertype = null)
	{
		lxshell_return("service", $drivertype, "stop");

		exec("chkconfig {$drivertype} off >/dev/null 2>&1");
		
		setRpmRemovedViaYum($drivertype);

		if (isServiceExists($drivertype)) {
			lunlink("/etc/init.d/{$drivertype}");
			lunlink("/usr/lib/systemd/system/{$drivertype}.service");
		}
	}

	static function installMeTrue($drivertype = null)
	{
		setRpmInstalled($drivertype);

		exec("chkconfig {$drivertype} on");

		createRestartFile("restart-web");
	}
}
