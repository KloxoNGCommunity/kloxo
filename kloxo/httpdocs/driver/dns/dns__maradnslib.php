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

	function createConfFile($action = null)
	{
		parent::createConfFileTrue('maradns', $action);
	}

	function syncCreateConf($action = null)
	{
		parent::syncCreateConfTrue('maradns', $action);
	}

	function createAllowTransferIps()
	{
		parent::createAllowTransferIpsTrue('maradns');
	}

	function dbactionAdd()
	{
		parent::dbactionAddTrue('maradns');
	}

	function dbactionUpdate($subaction)
	{
		parent::dbactionUpdateTrue('maradns', $subaction);
	}

	function dbactionDelete()
	{
		parent::dbactionDeleteTrue('maradns');
	}

	function dosyncToSystemPost()
	{
		 // MR -- no action here
	}
}