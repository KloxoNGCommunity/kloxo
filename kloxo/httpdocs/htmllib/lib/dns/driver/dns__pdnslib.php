<?php

include_once("dns__lib.php");

class dns__pdns extends dns__
{
	function __construct()
	{
		parent::__construct();
	}

	static function uninstallMe()
	{
		parent::uninstallMeTrue('pdns');
	}

	static function installMe()
	{
		parent::installMeTrue('pdns');
	}
}