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

	function createConfFile()
	{
		parent::createConfFileTrue('bind');
	}

	function syncCreateConf()
	{
		parent::syncCreateConfTrue('bind');
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
	}

	function dosyncToSystemPost()
	{
		 // MR -- no action here
	}
}