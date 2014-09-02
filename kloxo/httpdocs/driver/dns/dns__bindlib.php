<?php

include_once("dns__lib.php");

class dns__bind extends dns__
{
	function __construct()
	{
		parent::__construct();
	}

	static function unInstallMe()
	{
		parent::unInstallMeTrue('bind');
	}

	static function installMe()
	{
		parent::installMeTrue('bind');
	}

	function createConfFile($action = null)
	{
		parent::createConfFileTrue('bind', $action);
	}

	function syncCreateConf($action = null)
	{
		parent::syncCreateConfTrue('bind', $action);
	}

	function createAllowTransferIps()
	{
		parent::createAllowTransferIpsTrue('bind');
	}

	function dbactionAdd()
	{
		parent::dbactionAddTrue('bind');
	}

	function dbactionUpdate($subaction)
	{
		parent::dbactionUpdateTrue('bind', $subaction);
	}

	function dbactionDelete()
	{
		parent::dbactionDeleteTrue('bind');
		exec("rndc reconfig");
	}

	function dosyncToSystemPost()
	{
		 // MR -- no action here
	}
}