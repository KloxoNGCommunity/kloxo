<?php

include_once("dns__lib.php");

class dns__pdns extends dns__
{
	function __construct()
	{
		parent::__construct();
	}

	static function unInstallMe()
	{
		parent::unInstallMeTrue('pdns');
	}

	static function installMe()
	{
		parent::installMeTrue('pdns');
	}
}