<?php

include_once("dns__lib.php");

class dns__nsd extends dns__
{
	function __construct()
	{
		parent::__construct();
	}

	static function unInstallMe()
	{
		parent::unInstallMeTrue('nsd');
	}

	static function installMe()
	{
		parent::installMeTrue('nsd');
	}

	function createConfFile()
	{
		parent::createConfFileTrue('nsd');
	}

	function syncCreateConf()
	{
		parent::syncCreateConfTrue('nsd');
	}

	function createAllowTransferIps()
	{
		parent::createAllowTransferIpsTrue('nsd');
	}

	function dbactionAdd()
	{
		parent::dbactionAddTrue('nsd');
	}

	function dbactionUpdate($subaction)
	{
		parent::dbactionUpdateTrue('nsd', $subaction);
	}

	function dbactionDelete()
	{
		parent::dbactionDeleteTrue('nsd');
	}

	function dosyncToSystemPost()
	{
		 // MR -- no action here
	}
}