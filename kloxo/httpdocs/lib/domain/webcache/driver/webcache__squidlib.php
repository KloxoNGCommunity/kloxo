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
	}
}