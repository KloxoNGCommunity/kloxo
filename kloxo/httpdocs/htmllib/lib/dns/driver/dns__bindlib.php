<?php

include_once("dns__lib.php");

class dns__bind extends dns__
{
	function __construct()
	{
		parent::__construct();
	}

	static function uninstallMe()
	{
		parent::uninstallMeTrue('bind');
	}

	static function installMe()
	{
		parent::installMeTrue('bind');
	}
}