<?php

include_once("web__lib.php");

class web__monkeyproxy extends web__
{
	function __construct()
	{
		parent::__construct();
	}

	static function uninstallMe()
	{
		parent::uninstallMeTrue('monkeyproxy');
	}

	static function installMe()
	{
		parent::installMeTrue('monkeyproxy');
	}
}