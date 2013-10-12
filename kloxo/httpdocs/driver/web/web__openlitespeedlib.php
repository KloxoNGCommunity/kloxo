<?php

include_once("web__lib.php");

class web__openlitespeed extends web__
{
	function __construct()
	{
		parent::__construct();
	}

	static function uninstallMe()
	{
		parent::uninstallMeTrue('openlitespeed');
	}

	static function installMe()
	{
		parent::installMeTrue('openlitespeed');
	}
}