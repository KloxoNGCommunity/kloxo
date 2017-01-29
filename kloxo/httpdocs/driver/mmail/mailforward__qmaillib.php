<?php 

class Mailforward__Qmail  extends lxDriverClass
{
	function dbactionAdd()
	{
		global $gbl, $sgbl, $ghtml; 

	//	$domain = $this->main->getParentName();
		list($account, $dm) = explode("@", $this->main->nname);
		$domain = $dm;

		if (!$account) { return; }

		$mailpath = mmail__qmail::getDir($domain);
		$mailpath = str_replace($sgbl->__path_mail_root, $sgbl->__path_mail_data, $mailpath);

		$name = $account;
		$name = str_replace(".", ":", $name);

		$qmailfile = "{$mailpath}/.qmail-{$name}";
		$mailaccount = trim($this->main->forwardaddress);

		if (csb($mailaccount, "|")) {
			$tdat =  $mailaccount ;
		} else {
			$tdat = "&". $mailaccount ;
		}

		if (lxfile_exists("$mailpath/$account")) {
		//	throw new lxException($login->getThrow("mailaccount_exists"), '', $account);
			lxfile_rm($qmailfile);
		}

		lfile_write_content($qmailfile, $tdat, mmail__qmail::getUserGroup($domain));
	}

	function dbactionDelete()
	{
		global $gbl, $sgbl, $ghtml; 

	//	$domain = $this->main->getParentName();
		list($account, $dm) = explode("@", $this->main->nname);
		$domain = $dm;

		$mailpath = mmail__qmail::getDir($domain);
		$mailpath = str_replace($sgbl->__path_mail_root, $sgbl->__path_mail_data, $mailpath);

		$name = $account;
		$name = str_replace(".", ":", $name);

		lxfile_rm("$mailpath/.qmail-$name");
	}

	function dbactionUpdate($subaction)
	{
		$this->dbactionAdd();
	}
}
