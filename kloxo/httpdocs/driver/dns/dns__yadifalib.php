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

	function createConfFile()
	{
		parent::createConfFileTrue('yadifa');
	}

	function syncCreateConf()
	{
		parent::syncCreateConfTrue('yadifa');
	}

	function createAllowTransferIps()
	{
		parent::createAllowTransferIpsTrue('yadifa');
	}

	function dbactionAdd()
	{
		parent::dbactionAddTrue('yadifa');
	}

	function dbactionUpdate($subaction)
	{
		parent::dbactionUpdateTrue('yadifa', $subaction);
	}

	function dbactionDelete()
	{
		parent::dbactionDeleteTrue('yadifa');
	}

	function dosyncToSystemPost()
	{
		 // MR -- no action here
	}
}