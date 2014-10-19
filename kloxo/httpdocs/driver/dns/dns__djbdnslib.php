<?php

include_once("dns__lib.php");

class dns__djbdns extends dns__
{
	function __construct()
	{
		parent::__construct();
	}

	static function unInstallMe()
	{
		parent::unInstallMeTrue('djbdns');
	}

	static function installMe()
	{
		parent::installMeTrue('djbdns');
	}
}