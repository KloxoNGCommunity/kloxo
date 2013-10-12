<?php

include_once("dns__lib.php");

class dns__none extends dns__
{
	static function unInstallMe()
	{
	}

	static function installMe()
	{
	}

	static function copyConfigMe()
	{
	}

	function createConfFile($action = null)
	{
	}

	function syncCreateConf($action = null)
	{
	}

	function createAllowTransferIps()
	{
	}

	function dbactionAdd()
	{
	}

	function dbactionUpdate($subaction)
	{
	}

	function dbactionDelete()
	{
	}

	function dosyncToSystemPost()
	{
	}
}