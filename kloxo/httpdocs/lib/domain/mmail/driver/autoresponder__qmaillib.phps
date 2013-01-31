<?php  

class Autoresponder__Qmail extends lxDriverClass {


function createAutoResFile()
{
	global $gbl, $sgbl, $login, $ghtml; 

	$quser = explode("@", $this->main->nname);

	$mailpath = mmail__qmail::getDir($quser[1]);
	$mailpath = str_replace($sgbl->__path_mail_root, $sgbl->__path_mail_data, $mailpath);

	$domain = $quser[1];
	$sys_path = "$mailpath/$quser[0]";
	$sys_fpath = "$mailpath/$quser[0]"."/autorespond"."/message";

	lfile_write_content($sys_fpath, $this->main->text_message, mmail__qmail::getUserGroup($domain));
}


function dbActionAdd()
{
	//$this->createAutoResFile();
}

function dbActionDelete()
{
}

function dbactionUpdate($subaction)
{
	//$this->createAutoResFile();
}

}
