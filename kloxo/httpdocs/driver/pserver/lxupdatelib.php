<?php

class Lxupdate extends lxClass
{
	static $__ttype = "permanent";
	static $__desc = array("S", "",  "update");

	// Mysql
	static $__desc_nname = array("n", "",  "_version", "a=show");
	static $__desc_state = array("e", "",  "state", "a=show");
	static $__desc_schedule = array("n", "",  "_schedule_update_later", "a=show");

	static $__desc_detected_version_f = array("", "",  "detected_version");
	static $__desc_installed_rpm_version_f = array("", "",  "installed_rpm_version");
	static $__desc_latest_rpm_version_f = array("", "",  "latest_rpm_version");

	static $__desc_buglist_f = array("T", "",  "bugs_in_this_version");

	// MR -- add new var
	static $__desc_stamp_f = array("", "",  "stamp");
	static $__desc_step_f = array("", "",  "step");
	static $__desc_name_f = array("", "",  "Name");
	static $__desc_note_f = array("", "",  "Note");

	static $__desc_updatewarning_f = array("", "",  "Attention");

	static $__acdesc_update_lxupdateinfo = array("", "",  "update");
	static $__acdesc_update_bugs = array("", "",  "bugs");

	static $__desc_releasenote_l = array("", "",  "");

	function get(){}
	function write(){}

	function createShowPropertyList(&$alist)
	{
		$alist['property'][] = 'a=show';
		$alist['property'][] = "a=list&c=releasenote";
	}

	function createShowAlist(&$alist, $subaction = null)
	{
		global $gbl, $sgbl, $login, $ghtml;
		
		return $alist;
		
		// MR -- still used?
		if (checkIfLatest() && !if_demo()) {
			return null;
		}
	}

	function createShowUpdateform()
	{
		$uflist['lxupdateinfo'] = null;
		
		return $uflist;
	}

	function updateform($subaction, $param)
	{
		global $gbl, $sgbl, $login, $ghtml;
		
		$maj = $sgbl->__ver_major;

		switch($subaction) {
			case "lxupdateinfo":
				$vlist['name_f'] = array('M', $sgbl->__ver_name);
				$vlist['note_f'] = array('M', $sgbl->__ver_note);

				$vlist['detected_version_f'] = array('M', $sgbl->__ver_full);
				$vlist['installed_rpm_version_f'] = array('M', getInstalledVersion());
				$vlist['latest_rpm_version_f'] = array('M', getLatestVersion());

				if ($vlist['installed_rpm_version_f'] === $vlist['latest_rpm_version_f']) {
					$vlist['__v_button'] = array();
				} else {
					$vlist['updatewarning_f'] = array('W', $login->getKeywordUc('panel_update_warning'));
					$vlist['__v_button'] = "Update Now";
				}

				return $vlist;
			case "bugs":
				$file = "bugs/bugs-{$sgbl->__ver_major_minor_release}.txt";
				$content = curl_get_file_contents($file);
				$content = trim($content);
				
				if (!$content) {
					$content = "There are no Bugs Reported for this Version";
				}
				
				$vlist['buglist_f'] = array('t', $content);
				
				return $vlist;
		}
	}

	function updateLxupdateInfo()
	{
		global $login;

		if (isUpdating()) {
			throw new lxException($login->getThrow("program_is_already_updating"));
		} else {
			rl_exec_get($this->__masterserver, 'localhost', array('lxupdate', 'execUpdate'), null);

			throw new lxException($login->getThrow("update_scheduled"));
		}
	}

	static function execUpdate()
	{
		lxshell_background("__path_php_path", "../bin/update.php");
	}

	static function initThisObjectRule($parent, $class, $name = null)
	{
		global $gbl, $sgbl, $login, $ghtml;
		
	/*
		if (!$parent->isLocalhost('nname')) {
			throw new lxException($login->getThrow("slave_is_automatically_updated"), '', $parent->nname);
		}
	*/
		
		$thisversion = $sgbl->__ver_major_minor_release;
		$upversion = getLatestVersion();
		
		return $upversion;
	}
}

