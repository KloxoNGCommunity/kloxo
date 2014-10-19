<?php

include_once("dns__lib.php");

class dns__nsd extends dns__
{
	function __construct()
	{
		parent::__construct();
	}

	static function unInstallMe()
	{
		parent::unInstallMeTrue('nsd');
	}

	static function installMe()
	{
		parent::installMeTrue('nsd');
	}
}