<?php

include_once("web__lib.php");

class web__hiawatha extends web__
{
	function __construct()
	{
		parent::__construct();
	}

	static function uninstallMe()
	{
		parent::uninstallMeTrue('hiawatha');
	}

	static function installMe()
	{
		parent::installMeTrue('hiawatha');
	}
}