<?php

include_once("web__lib.php");

class web__monkey extends web__
{
	function __construct()
	{
		parent::__construct();
	}

	static function uninstallMe()
	{
		parent::uninstallMeTrue('monkey');
	}

	static function installMe()
	{
		parent::installMeTrue('monkey');
	}
}