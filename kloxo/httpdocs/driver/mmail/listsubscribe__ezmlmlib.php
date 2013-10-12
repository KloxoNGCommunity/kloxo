<?php

class listsubscribe__ezmlm extends lxDriverClass
{
	function dbactionAdd()
	{
		global $gbl, $sgbl, $login, $ghtml;

		$var = explode('@', $this->main->getParentName());

		$mailpath = mmail__qmail::getDir($var[1]);
		$mailpath = str_replace($sgbl->__path_mail_root, $sgbl->__path_mail_data, $mailpath);

		$user = mmail__qmail::getUserGroup($var[1]);

		$ad = trim($this->main->address);
		$adl = explode("\n", $ad);
		
		foreach ($adl as $a) {
			$a = trim($a);

			if (!$a) {
				continue;
			}

			lxuser_return($user, "/usr/bin/ezmlm-sub","$mailpath/{$var[0]}/", $a);
		}
	}

	function dbactionDelete()
	{
		global $gbl, $sgbl, $login, $ghtml;

		$var = explode('@', $this->main->getParentName());

		$mailpath = mmail__qmail::getDir($var[1]);
		$mailpath = str_replace($sgbl->__path_mail_root, $sgbl->__path_mail_data, $mailpath);

		$user = mmail__qmail::getUserGroup($var[1]);

		lxuser_return($user, "/usr/bin/ezmlm-unsub", "$mailpath/{$var[0]}/", $this->main->address);
	}

	static function readSubscribeList($listname)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$var = explode('@', $listname);

		$mailpath = mmail__qmail::getDir($var[1]);
		$mailpath = str_replace($sgbl->__path_mail_root, $sgbl->__path_mail_data, $mailpath);

		$list = lxshell_output("/usr/bin/ezmlm-list", "$mailpath/{$var[0]}");

		$list = explode("\n", $list);

		$res = null;
		
		foreach ($list as $r) {
			if (!$r) {
				continue;
			}
			
			$r = trim($r);
			//$re['nname'] = "{$r}___$listname";
			$re['nname'] = $r;
			$re['address'] = $r;
			$res[] = $re;
		}

		return $res;
	}
}

