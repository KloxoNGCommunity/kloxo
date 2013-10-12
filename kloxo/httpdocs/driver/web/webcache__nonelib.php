<?php

include_once("webcache__lib.php");

class webcache__none extends webcache__
{
	function __construct()
	{
		parent::__construct();
	}

	static function uninstallMe()
	{
		// no action because 'none' driver
	}

	static function installMe()
	{
		// no action because 'none' driver
	}

	static function copyConfig()
	{
		// no action because 'none' driver
	}
}