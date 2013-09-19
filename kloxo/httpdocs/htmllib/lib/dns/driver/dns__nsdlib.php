<?php

include_once("dns__lib.php");

class dns__nsd extends dns__
{
	function __construct()
	{
		parent::__construct();
	}

	static function uninstallMe()
	{
		parent::uninstallMeTrue('nsd');
	}

	static function installMe()
	{
		parent::installMeTrue('nsd');
	}
}