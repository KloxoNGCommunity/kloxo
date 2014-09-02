<?php

include_once("dns__lib.php");

class dns__djbdns extends dns__
{
	function __construct()
	{
		parent::__construct();
	}

	static function unInstallMe()
	{
		parent::unInstallMeTrue('djbdns');
	}

	static function installMe()
	{
		parent::installMeTrue('djbdns');
	}

	function createConfFile($action = null)
	{
		parent::createConfFileTrue('djbdns', $action);
	}

	function syncCreateConf($action = null)
	{
		parent::syncCreateConfTrue('djbdns', $action);
	}

	function createAllowTransferIps()
	{
		parent::createAllowTransferIpsTrue('djbdns');
	}

	function dbactionAdd()
	{
		parent::dbactionAddTrue('djbdns');
	}

	function dbactionUpdate($subaction)
	{
		parent::dbactionUpdateTrue('djbdns', $subaction);
	}

	function dbactionDelete()
	{
		parent::dbactionDeleteTrue('djbdns');
	}

	function dosyncToSystemPost()
	{
		 // MR -- no action here
	}
}