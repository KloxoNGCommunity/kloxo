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
	//	parent::uninstallMeTrue('none');
	}

	static function installMe()
	{
		// no action because 'none' driver
	//	parent::installMeTrue('none');
	}
}