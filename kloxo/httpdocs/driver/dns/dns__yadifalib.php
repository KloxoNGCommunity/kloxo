<?php

include_once("dns__lib.php");

class dns__yadifa extends dns__
{
	function __construct()
	{
		parent::__construct();
	}

	static function unInstallMe()
	{
		parent::unInstallMeTrue('yadifa');
	}

	static function installMe()
	{
		parent::installMeTrue('yadifa');
	}
}