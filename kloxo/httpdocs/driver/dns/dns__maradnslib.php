<?php

include_once("dns__lib.php");

class dns__maradns extends dns__
{
	function __construct()
	{
		parent::__construct();
	}

	static function unInstallMe()
	{
		parent::unInstallMeTrue('maradns');
	}

	static function installMe()
	{
		parent::installMeTrue('maradns');
	}
}