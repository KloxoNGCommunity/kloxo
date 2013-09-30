<?php

include_once("webcache__lib.php");

class webcache__varnish extends webcache__
{
	function __construct()
	{
		parent::__construct();
	}

	static function uninstallMe()
	{
		parent::uninstallMeTrue('varnish');
	}

	static function installMe()
	{
		parent::installMeTrue('varnish');
	}
}