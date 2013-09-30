<?php

class WebCache extends LxDriverClass
{
	static $__desc = array("", "", "webcache");

	static function switchProgramPre($old, $new)
	{
		exec_class_method("webcache__{$old}", "uninstallMe");
		exec_class_method("webcache__{$new}", "installMe");
	}

	static function switchProgramPost($old, $new)
	{
		createRestartFile($new);
	}

	static function removeOtherDriver()
	{
		global $gbl, $sgbl, $login, $ghtml;

		removeOtherDrivers($class = 'webcache', $nolog = true);
	}

	function inheritSynserverFromParent() { return false; }

	function updateform($subaction, $param)
	{
	}

	function postUpdate()
	{
	}
}