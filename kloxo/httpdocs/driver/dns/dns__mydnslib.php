<?php

include_once("dns__lib.php");

class dns__mydns extends dns__
{
	function __construct()
	{
		parent::__construct();
	}

	static function unInstallMe()
	{
		parent::unInstallMeTrue('mydns');
	}

	static function installMe()
	{
		parent::installMeTrue('mydns');
	}
}