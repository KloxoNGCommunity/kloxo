<?php

include_once("web__lib.php");

class web__gwan extends web__
{
	function __construct()
	{
		parent::__construct();
	}

	static function uninstallMe()
	{
		parent::uninstallMeTrue('gwan');
	}

	static function installMe()
	{
		parent::installMeTrue('gwan');
	}
}