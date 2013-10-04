<?php

include_once("web__lib.php");

class web__openlitespeedproxy extends web__
{
	function __construct()
	{
		parent::__construct();
	}

	static function uninstallMe()
	{
		parent::uninstallMeTrue('openlitespeedproxy');
	}

	static function installMe()
	{
		parent::installMeTrue('openlitespeedproxy');
	}
}
