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
	}
}