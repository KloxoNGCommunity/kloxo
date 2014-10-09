<?php

class phpini__sync extends Lxdriverclass
{
	function initString()
	{
		//	$pclass = $this->main->getParentClass();

		$this->main->fixphpIniFlag();

		//	$this->setInitString();

	}

	function setInitString()
	{
		$modulelist = array('xcache', 'suhosin', 'ioncube', 'zend');

		foreach ($modulelist as &$m) {
			if ($this->main->phpini_flag_b->isOn("enable_{$m}_flag")) {
				$active = isPhpModuleActive($m);

				if ($active) {
					setPhpModuleActive($m);
				}
			} else {
				setPhpModuleInactive($m);
			}
		}
	}

	function enableDisableModule($flag, $mod)
	{
		// MR -- not used since 6.2.x
	}

	function createIniFile()
	{
		$pclass = $this->main->getParentClass();

		$this->initString();

		$l1 = $this->main->getInheritedList();
		$l2 = $this->main->getLocalList();
		$l3 = $this->main->getExtraList();

		$ll = lx_array_merge(array($l1, $l2, $l3));
		$list = array_unique($ll);

		$input = array();

		foreach ($list as &$l) {
			$input[$l] = (isset($this->main->phpini_flag_b->$l)) ? $this->main->phpini_flag_b->$l : '';
		}

		$user = $input['user'] = (isset($this->main->__var_web_user)) ? $this->main->__var_web_user : 'apache';

		$phpini_path = "/opt/configs/phpini/tpl";
		$phpini_cont = file_get_contents(getLinkCustomfile($phpini_path, "php.ini.tpl"));

		$fcgid_path = "/opt/configs/apache/tpl";
		$fcgid_cont = file_get_contents(getLinkCustomfile($fcgid_path, "php5.fcgi.tpl"));

		$phpfpm_path_etc = "/opt/configs/php-fpm/etc";
		$phpfpm_path = "/opt/configs/php-fpm/tpl";
		$phpfpm_cont = file_get_contents(getLinkCustomfile($phpfpm_path, "php53-fpm-pool.conf.tpl"));
		$phpfpm_main = getLinkCustomfile($phpfpm_path_etc, "php53-fpm.conf");
		$phpfpm_www = getLinkCustomfile($phpfpm_path_etc . "/php-fpm.d", "www.conf");

		$htaccess_path = "/opt/configs/phpini/tpl";
		$htaccess_cont = file_get_contents(getLinkCustomfile($htaccess_path, "htaccess.tpl"));

		if (!file_exists("/etc/php-fpm.d")) {
			lxfile_mkdir("/etc/php-fpm.d");
		}

		$stlist[] = "###Start Kloxo PHP config Area";
		$stlist[] = "###Start Lxdmin Area";
		$stlist[] = "###Start Kloxo Area";
		$stlist[] = "###Start Lxadmin PHP config Area";

		$endlist[] = "###End Kloxo PHP config Area";
		$endlist[] = "###End Kloxo Area";
		$endlist[] = "###End Lxadmin PHP config Area";

		$endstring = $endlist[0];
		$startstring = $stlist[0];

		if ($pclass === 'pserver') {
			$phpini_parse = getParseInlinePhp($phpini_cont, $input);
			$fcgid_parse = getParseInlinePhp($fcgid_cont, $input);
			$phpfpm_parse = getParseInlinePhp($phpfpm_cont, $input);

			$phpini_target = '/etc/php.ini';
			$fcgid_target = '/home/kloxo/client/php5.fcgi';
			$phpfpm_target = '/etc/php-fpm.d/default.conf';

			file_put_contents($phpini_target, $phpini_parse);
			file_put_contents($fcgid_target, $fcgid_parse);
			file_put_contents($phpfpm_target, $phpfpm_parse);

			lxfile_cp($phpfpm_main, "/etc/php-fpm.conf");
			lxfile_cp($phpfpm_www, "/etc/php-fpm.d/www.conf");

			lxfile_unix_chmod($fcgid_target, "0755");
		} else {
			$input['phpinipath'] = "/home/kloxo/client/{$user}";
			$input['phpcgipath'] = "/usr/bin/php-cgi";

			if ($pclass === 'client') {
				$maxchildren = db_get_value("client", $user, "priv_q_phpfcgiprocess_num");

				if (($maxchildren === 'Unlimited') || ($maxchildren === '-')) {
					$maxchildren = '6';
				}

				$input['maxchildren'] = $maxchildren;

				$phpini_parse = getParseInlinePhp($phpini_cont, $input);
				$fcgid_parse = getParseInlinePhp($fcgid_cont, $input);
				$phpfpm_parse = getParseInlinePhp($phpfpm_cont, $input);

				$phpini_target = "/home/kloxo/client/{$user}/php.ini";
				$fcgid_target = "/home/kloxo/client/{$user}/php5.fcgi";
				$phpfpm_target = "/etc/php-fpm.d/{$user}.conf";

				file_put_contents($phpini_target, $phpini_parse);
				file_put_contents($fcgid_target, $fcgid_parse);
				file_put_contents($phpfpm_target, $phpfpm_parse);

				lxfile_unix_chmod($fcgid_target, "0755");
			} else {
				$htaccess_parse = getParseInlinePhp($htaccess_cont, $input);

				$htaccess_target = "/home/{$user}/kloxoscript/.htaccess";

				file_put_between_comments("{$user}:apache", $stlist, $endlist,
					$startstring, $endstring, $htaccess_target, $htaccess_parse);

				lxfile_unix_chown($htaccess_target, "{$user}:apache");
			}
		}

		/*
			// MR -- also restart php-fpm
			$phptype = db_get_value("serverweb", "pserver-" . $this->syncserver, "php-type");
			if (strpos($phptype, 'php-fpm') !== false) {
				createRestartFile('php-fpm');
			}

			createRestartFile($this->main->__var_webdriver);
		*/
		createRestartFile("restart-web");
	}

	function createHtaccessFile()
	{
		$pclass = $this->main->getParentClass();

		$this->initString();

		$l1 = $this->main->getInheritedList();
		$l2 = $this->main->getLocalList();
		$l3 = $this->main->getExtraList();

		$ll = lx_array_merge(array($l1, $l2, $l3));
		$list = array_unique($ll);

		$input = array();

		foreach ($list as &$l) {
			$v = $this->main->phpini_flag_b->$l;
			$input[$l] = ($v) ? $v : '';
		}

		$user = $input['user'] = (isset($this->main->__var_web_user)) ? $this->main->__var_web_user : 'apache';

		$stlist[] = "###Start Kloxo PHP config Area";
		$stlist[] = "###Start Lxdmin Area";
		$stlist[] = "###Start Kloxo Area";
		$stlist[] = "###Start Lxadmin PHP config Area";

		$endlist[] = "###End Kloxo PHP config Area";
		$endlist[] = "###End Kloxo Area";
		$endlist[] = "###End Lxadmin PHP config Area";

		$endstring = $endlist[0];
		$startstring = $stlist[0];

		$phpini_path = "/opt/configs/phpini/tpl";

		if ($pclass !== 'pserver') {
			$htaccess_cont = file_get_contents(getLinkCustomfile($phpini_path, "htaccess.tpl"));
			$htaccess_parse = getParseInlinePhp($htaccess_cont, $input);

			$htaccess_target = "{$droot}/.htaccess";

			file_put_between_comments("{$user}:apache", $stlist, $endlist,
				$startstring, $endstring, $htaccess_target, $htaccess_parse);

			lxfile_unix_chown($htaccess_target, "{$user}:apache");
		}
	}

	function dbactionAdd()
	{
		$this->createIniFile();

		// MR -- also restart php-fpm
		$phptype = db_get_value("serverweb", "pserver-" . $this->syncserver, "php-type");
		if (strpos($phptype, 'php-fpm') !== false) {
			createRestartFile('php-fpm');
		}
	}

	function updateSelected()
	{
		// MR -- simple way
		$this->createIniFile();
	}

	function dbactionUpdate($subaction)
	{
		switch ($subaction) {
			case "full_update":
				$this->createIniFile();
				$this->createHtaccessFile();
				break;
			case "ini_update":
				$this->createIniFile();
				break;
			case "htaccess_update":
				$this->createHtaccessFile();
				break;
			default:
				$this->updateSelected();
				break;
		}
	}

}
