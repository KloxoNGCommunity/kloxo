<?php

include_once("dns__lib.php");

class dns__pdns extends dns__
{
	function __construct()
	{
		parent::__construct();
	}

	static function unInstallMe()
	{
		parent::unInstallMeTrue('pdns');
	}

	static function installMe()
	{
		parent::installMeTrue('pdns');
	}

	function createConfFile($action = null)
	{
		parent::createConfFileTrue('pdns');
	}

	function syncAddFile($domainname, $action = null)
	{
		parent::syncAddFileTrue('pdns');
	}

	function dbactionAdd()
	{
		parent::dbactionAddTrue('pdns');
	}

	function dbactionUpdate($subaction)
	{
		parent::dbactionUpdateTrue('pdns');
	}

	function dbactionDelete()
	{
		parent::dbactionDeleteTrue('pdns');
	}

	function dosyncToSystemPost()
	{
		 // MR -- no action here
	}
}