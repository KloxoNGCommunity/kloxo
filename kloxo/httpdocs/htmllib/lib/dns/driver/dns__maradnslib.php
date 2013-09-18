<?php

include_once("dns__lib.php");

class dns__maradns extends dns__
{
	function __construct()
	{
		parent::__construct();
	}

	static function uninstallMe()
	{
		parent::uninstallMeTrue('maradns');
	}

	static function installMe()
	{
		parent::installMeTrue('maradns');
	}
}