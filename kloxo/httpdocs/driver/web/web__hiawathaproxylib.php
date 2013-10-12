<?php

include_once("web__lib.php");

class web__hiawathaproxy extends web__
{
	function __construct()
	{
		parent::__construct();
	}

	static function uninstallMe()
	{
		parent::uninstallMeTrue('hiawathaproxy');
	}

	static function installMe()
	{
		parent::installMeTrue('hiawathaproxy');
	}
}
