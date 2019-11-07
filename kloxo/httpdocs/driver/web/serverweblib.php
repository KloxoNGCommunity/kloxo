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
	static $__desc_enable_keepalive = array("f", "", "enable_keepalive");

	static $__desc_mysql_convert = array("", "", "mysql_convert");
	static $__desc_mysql_charset = array("", "", "mysql_charset");

	static $__desc_fix_chownchmod = array("", "", "fix_chownchmod");
	static $__desc_fix_chownchmod_user = array("", "", "fix_chownchmod");

	static $__desc_php_branch = array("", "", "php_branch");
	static $__desc_php_used = array("", "", "php_used");

	static $__desc_multiple_php_flag = array("f", "", "multiple_php_enable");

	static $__desc_multiple_php_install = array("", "", "multiple_php_install");
	static $__desc_multiple_php_already_installed = array("", "", "multiple_php_already_installed");
	static $__desc_multiple_php_remove = array("", "", "multiple_php_remove");

	static $__desc_pagespeed_cache = array("", "", "pagespeed_cache");

	static $__desc_enable_php52m_fpm = array("", "", "enable_php52m_fpm");

	function createShowUpdateform()
	{
		global $login;

		if ($this->getParentO()->getClass() === 'pserver') {
			$uflist['edit'] = null;

			$uflist['php_used'] = null;

			$uflist['php_branch'] = null;

			$uflist['multiple_php_activate'] = null;
			$uflist['multiple_php_install'] = null;
			$uflist['multiple_php_remove'] = null;

			$a = getListOnList('php');

			foreach ($a as $k => $v) {
				if (strpos($v, 'php52') !== false) {
					$uflist['enable_php52m_fpm'] = null;

					break;
				}
			}

			if (isWebProxyOrApache()) {
				$uflist['php_type'] = null;
				$uflist['apache_optimize'] = null;
			}

			$uflist['mysql_convert'] = null;

			$uflist['fix_chownchmod'] = null;

			$uflist['pagespeed_clear_cache'] = null;
		} else {
			$uflist['fix_chownchmod_user'] = null;
		}

		return $uflist;
	}

	function updateform($subaction, $param)
	{
		switch($subaction) {
			case "apache_optimize":
				$this->apache_optimize = null;

				$out = null;
				exec("cat /etc/httpd/conf.d/~lxcenter.conf | grep -i '### selected:'", $out);

				if (count($out) > 0) {
					if (strpos($out[0], 'customize') !== false) {
						$a = array('default', 'low', 'medium', 'high', 'customize');
					} else {
						$a = array('default', 'low', 'medium', 'high');
					}
				} else {
					$a = array('default', 'low', 'medium', 'high');
				}
			
				$vlist['apache_optimize'] = array('s', $a);

				$b = '';

				if (count($out) > 0) {
					foreach ($a as $k => $v) {
						if (strpos($out[0], $v) !== false) {
							$b = $v;

							break;
						}
					}
				}

				if ($b !== '') {
					$this->setDefaultValue('apache_optimize', $b);
				}

				$this->enable_keepalive = null;

				$vlist['enable_keepalive'] = null;

				$out = null;
				exec("cat /etc/httpd/conf.d/~lxcenter.conf | grep -i ^'keepalive on'", $out);

				if (count($out) > 0) {
					$s = 'on';
				} else {
					$s = 'off';
				}

				$this->setDefaultValue('enable_keepalive', $s);

				break;
			case "mysql_convert":
				$this->mysql_convert = null;
				$this->mysql_charset = null;

				// TODO: "SELECT ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'kloxo';"
				// mysql -u[user] -p -D[database] -e "show table status\G"| egrep "(Index|Data)_length" | awk 'BEGIN { rsum = 0 } { rsum += $2 } END { print rsum }'

				if (getRpmBranchInstalled('mysql') === 'MariaDB-server') {
					$vlist['mysql_convert'] = array('s', array('to-myisam', 'to-innodb', 'to-aria', 'to-tokudb'));
				} else {
					$vlist['mysql_convert'] = array('s', array('to-myisam', 'to-innodb'));
				}
	
				$vlist['mysql_charset'] = array('s', array( 'utf8'));

				break;
			case "fix_chownchmod":
				$this->fix_chownchmod = null;

				$vlist['fix_chownchmod'] = array('s', array('fix-ownership', 'fix-permissions', 'fix-ALL'));

				break;

			case "fix_chownchmod_user":
				$this->fix_chownchmod_user = null;

				$vlist['fix_chownchmod_user'] = array('s', array('fix-ownership', 'fix-permissions', 'fix-ALL'));

				break;
			case "php_type":
				$this->php_type = null;
				$this->secondary_php = null;

				$a = array('suphp_event', 'suphp_worker',
					'php-fpm_event', 'php-fpm_worker',
					'fcgid_event', 'fcgid_worker');

				if (file_exists("/etc/httpd/modules/libphp5.so")) {
					// MR -- remove mod_php on 'php-type' select
					$a = array_merge(array('mod_php_ruid2', 'mod_php_itk','suphp'), $a);
				}

				if (file_exists("../etc/flag/use_apache24.flg")) {
					$a = array_merge($a, array('proxy_fcgi_event', 'proxy_fcgi_worker'));

				}

				$vlist['php_type'] = array('s', $a);

				$d = db_get_value("serverweb", "pserver-". $this->syncserver, "php_type");
	
				if (!$d) {
					db_set_default("serverweb", "php_type", "php-fpm_event", 
						"nname = 'pserver-{$this->syncserver}'");
					$this->setDefaultValue('php_type', 'php-fpm_event');
				} else {
					$this->setDefaultValue('php_type', $d);
				}

				$vlist['secondary_php'] = array('f', array('on', 'off'));

				if (file_exists("/etc/httpd/conf.d/suphp2.conf")) {
					$this->setDefaultValue('secondary_php', 'on');
				}

				if (file_exists("/etc/httpd/conf.d/suphp52.conf")) {
					lxfile_rm("/etc/httpd/conf.d/suphp52.conf");
				}

				break;
			case "php_branch":
				$this->php_branch = null;

				$a = getListOnList('php');
				$vlist['php_branch'] = array('s', $a);

				$this->setDefaultValue('php_branch', getRpmBranchInstalledOnList('php'));

				break;
			case "multiple_php_install":
				$this->multiple_php_already_installed = null;
				$this->multiple_php_install = null;

			//	$a = rl_exec_get(null, $this->syncserver, "getCleanRpmBranchListOnList", array('php'));
				$a = getCleanRpmBranchListOnList('php');

			//	$g = rl_exec_get(null, $this->syncserver, "getMultiplePhpList");
				$g = getMultiplePhpList();

				$u = array_diff($a, $g);

				$vlist['multiple_php_install'] = array("U", $u);

				break;

			case "multiple_php_remove":
				$this->multiple_php_remove = null;

				$a = getMultiplePhpList();

				$vlist['multiple_php_remove'] = array("U", $a);

				break;

			case "multiple_php_activate":
				$h = implode(" ", getMultiplePhpList());

				$vlist['multiple_php_already_installed'] = array("M", $h);

				$vlist['multiple_php_flag'] = array("f", array('on', 'off'));

				$this->multiple_php_flag = null;

				$s = (file_exists("../etc/flag/enablemultiplephp.flg")) ? 'on' : 'off';

				$this->setDefaultValue('multiple_php_flag', $s);

				break;

			case "php_used":
				$this->php_used = null;

				$d = getMultiplePhpList();
				$g = getInitialPhpFpmConfig();

				$s = '--PHP Branch--';
            
				if (isset($d)) {
					foreach ($d as $k => $v) {
						if ($v === 'php52m') {
							unset($d[$k]);
						}
					}

					$d = array_merge(array($s), $d);
				} else { 
					$d = array($s);
				}

				if ($g === 'php') {
					$j = $s;
				} else {
					$j = $g;
				}

				$this->setDefaultValue('php_used', $j);

				$vlist['php_used'] = array('s', $d);

				break;

			case "enable_php52m_fpm":

				$this->enable_php52m_fpm = null;

				$vlist['enable_php52m_fpm'] = array("f", array('on', 'off'));

				if (file_exists("../etc/flag/enable_php52m-fpm.flg")) {
					$this->setDefaultValue('enable_php52m_fpm', 'on');
				}

				break;

			case "pagespeed_clear_cache":
				$this->pagespeed_cache = null;

				$vlist['pagespeed_cache'] = array('s', array( 'clear'));

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
