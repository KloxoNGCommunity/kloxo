<?php

class serverweb extends lxdb
{
	static $__desc = array("", "", "webserver_config");
	static $__desc_nname = array("", "", "webserver_config");
	static $__desc_php_type = array("", "", "php_type");
	static $__desc_secondary_php = array("", "", "secondary_php");

	static $__acdesc_update_edit = array("", "", "config");
	static $__acdesc_show = array("", "", "webserver_config");

	static $__desc_apache_optimize = array("", "", "apache_optimize");

	static $__desc_mysql_convert = array("", "", "mysql_convert");
	static $__desc_mysql_charset = array("", "", "mysql_charset");

	static $__desc_fix_chownchmod = array("", "", "fix_chownchmod");

	static $__desc_php_branch = array("", "", "php_branch");

	function createShowUpdateform()
	{
		$uflist['edit'] = null;

		$uflist['php_branch'] = null;

		if (isWebProxyOrApache()) {
			$uflist['php_type'] = null;
			$uflist['apache_optimize'] = null;
		}

		$uflist['mysql_convert'] = null;
		$uflist['fix_chownchmod'] = null;

		return $uflist;
	}

	function updateform($subaction, $param)
	{
		global $gbl, $sgbl, $login, $ghtml;

		switch($subaction) {
			case "apache_optimize":
				$vlist['apache_optimize'] = array('s', array('---No Change---', 'default', 'optimize'));
				$this->setDefaultValue('apache_optimize', '---No Change---');

				break;
			case "mysql_convert":
				if (getRpmBranchInstalled('mysql') === 'MariaDB-server') {
					$vlist['mysql_convert'] = array('s', array('---No Change---', 'to-myisam', 'to-innodb', 'to-aria'));
				} else {
					$vlist['mysql_convert'] = array('s', array('---No Change---', 'to-myisam', 'to-innodb'));
				}
				
				$this->setDefaultValue('mysql_convert', '---No Change---');

				$vlist['mysql_charset'] = array('s', array('---No Change---', 'utf-8'));

				$this->setDefaultValue('mysql_charset', '---No Change---');

				break;
			case "fix_chownchmod":
				$vlist['fix_chownchmod'] = array('s', array('---No Change---', 'fix-ownership', 'fix-permissions', 'fix-ALL'));

				$this->setDefaultValue('fix_chownchmod', '---No Change---');

				break;
			case "php_type":
				if (!db_get_value("serverweb", "pserver-". $login->syncserver, "php_type")) {
					db_set_default("serverweb", "php_type", "php-fpm_event", 
						"nname = 'pserver-{$login->syncserver}'");
					$this->setDefaultValue('php_type', 'php-fpm_event');
				}

				// MR -- remove mod_php on 'php-type' select
				$vlist['php_type'] = array('s', array(
					'mod_php_ruid2', 'mod_php_itk',
					'suphp', 'suphp_event', 'suphp_worker',
					'php-fpm_event', 'php-fpm_worker',
					'fcgid_event', 'fcgid_worker'));
	
				$vlist['secondary_php'] = array('f', 'on', 'off');

				if (file_exists("/etc/httpd/conf.d/suphp52.conf")) {
					$this->setDefaultValue('secondary_php', 'on');
				}

				break;
			case "php_branch":
				$a = getRpmBranchListOnList('php');
				$vlist['php_branch'] = array('s', $a);

				$this->setDefaultValue('php_branch', getRpmBranchInstalledOnList('php'));

				break;
			default:
				$vlist['__v_button'] = array();

				break;
		}

		return $vlist;
	}

	static function initThisObjectRule($parent, $class, $name = null)
	{
		return $parent->getClName();
	}

}
