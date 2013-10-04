<?php

include_once("dns__lib.php");

class dns__none extends dns__
{
	function __construct()
	{
		parent::__construct();
	}

	static function uninstallMe()
	{
		parent::uninstallMeTrue('none');
	}

	static function installMe()
	{
		parent::installMeTrue('none');
	}
}