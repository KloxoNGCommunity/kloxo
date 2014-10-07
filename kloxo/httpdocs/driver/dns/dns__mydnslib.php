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

	function createConfFile()
	{
		parent::createConfFileTrue('mydns');
	}

	function syncCreateConf()
	{
		parent::syncCreateConfTrue('mydns');
	}

	function createAllowTransferIps()
	{
		parent::createAllowTransferIpsTrue('mydns');
	}

	function dbactionAdd()
	{
		parent::dbactionAddTrue('mydns');
	}

	function dbactionUpdate($subaction)
	{
		parent::dbactionUpdateTrue('mydns', $subaction);
	}

	function dbactionDelete()
	{
		parent::dbactionDeleteTrue('mydns');
	}

	function dosyncToSystemPost()
	{
		 // MR -- no action here
	}
}