<?php

class phpini__sync extends Lxdriverclass
{
	function initString()
	{
	//	$pclass = $this->main->getParentClass();

		$this->main->fixphpIniFlag();

		$this->setInitString();

	}

	function setInitString()
	{
		$phpver = getPhpVersion();
		$phpbranch = getPhpBranch();

		// MR -- for zend
		if (version_compare($phpver, "5.3.0", ">=")) {
			$branchlist = array("{$phpbranch}-zend-guard-loader", "{$phpbranch}-zend-guard",
				"php-zend-guard-loader", "php-zend-guard");
		} else {
			$branchlist = array("{$phpbranch}-zend-optimizer-loader", "{$phpbranch}-zend-optimizer",
				"php-zend-optimizer", "php-zend");
		}

		$inilist = array('zend', 'zendoptimizer', 'zendguard');

		$this->setPhpModule('zend', $branchlist, $inilist);

		// MR -- for xcache
		$branchlist = array("{$phpbranch}-xcache", "php-xcache");
		$inilist = array('xcache');

		$this->setPhpModule('xcache', $branchlist, $inilist);

		// MR -- for ioncube
		$branchlist = array("{$phpbranch}-ioncube-loader", "php-ioncube-loader", "php-ioncube");
		$inilist = array('ioncube', 'ioncube-loader');

		$this->setPhpModule('ioncube', $branchlist, $inilist);

		// MR -- for suhosin
		$branchlist = array("{$phpbranch}-suhosin", "php-suhosin");
		$inilist = array('suhosin');

		$this->setPhpModule('suhosin', $branchlist, $inilist);
	}

	function setPhpModule($module, $branchlist, $inilist)
	{
		$list = $branchlist;

		$phpdpath = "/etc/php.d";

		$installed = false;

		foreach ($list as &$l) {
			$installed = isRpmInstalled($l);

			if ($installed) {
				break;
			}
		}

		if (file_exists("/usr/local/lxlabs/kloxo/etc/flag/enable_{$module}.flg")) {
			if (!$installed) {
				foreach ($list as &$l) {
					$f = "/home/rpms/{$l}-*.rpm";
					$flist = glob($f);

					$ret = true;

					if ($flist) {
						$ret = lxshell_return("rpm", "-ivh", "--replacefiles", $f);
					} else {
						$ret = lxshell_return("yum", "-y", "install", $l);
					}

					if ($ret) {
						throw new lxException("install_{$l}_failed", 'parent');
					}
				}
			}
		} else {
			if ($installed) {
				foreach ($inilist as &$i) {
					if (file_exists("{$phpdpath}/{$i}.ini")) {
						lxfile_mv("{$phpdpath}/{$i}.ini", "{$phpdpath}/{$i}.nonini");
					}
				}
			}
		}
	}

	function enableDisableModule($flag, $mod)
	{
		// MR -- not used since 6.2.x
	}

	function createIniFile()
	{
		global $sgbl;

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

		$stlist[] = "###Start Kloxo PHP config Area";
		$stlist[] = "###Start Lxdmin Area";
		$stlist[] = "###Start Kloxo Area";
		$stlist[] = "###Start Lxadmin PHP config Area";

		$endlist[] = "###End Kloxo PHP config Area";
		$endlist[] = "###End Kloxo Area";
		$endlist[] = "###End Lxadmin PHP config Area";

		$endstring = $endlist[0];
		$startstring = $stlist[0];

		$fpath = "/usr/local/lxlabs/kloxo/file";
		$tpath = "/home/phpini/tpl";

		exec("cp -rf {$fpath}/phpini /home");

		$pcont = file_get_contents(getLinkCustomfile($tpath, "php.ini.tpl"));
		$hcont = file_get_contents(getLinkCustomfile($tpath, "htaccess.tpl"));

		$pparse = getParseInlinePhp($pcont, $input);
		$hparse = getParseInlinePhp($hcont, $input);

		if ($pclass === 'pserver') {
			$ptarget = '/etc/php.ini';
			file_put_contents($ptarget, $pparse);
		} else {
			$dname = $this->main->getParentName();
			$hroot = $sgbl->__path_httpd_root;
			$droot = $this->main->__var_docrootpath;
			$wuser = $this->main->__var_web_user;
			$cname = $this->main->__var_customer_name;

		//	$elogfile = "/home/{$cname}/__processed_stats/{$dname}.phplog";

			$ptarget = "{$hroot}/{$dname}/php.ini";
			file_put_contents($ptarget, $pparse);

			$htfile = "{$droot}/.htaccess";
			$ht1file = "/home/{$cname}/kloxoscript/.htaccess";

			file_put_between_comments("{$wuser}:apache", $stlist, $endlist, 
					$startstring, $endstring, $htfile, $hparse);
			file_put_between_comments("{$wuser}:apache", $stlist, $endlist, 
					$startstring, $endstring, $ht1file, $hparse);

			lxfile_unix_chown($htfile, "{$wuser}:apache");

		}

		// MR -- also restart php-fpm
		createRestartFile('php-fpm');

		createRestartFile($this->main->__var_webdriver);

	}

	function dbactionAdd()
	{
		$this->createIniFile();
	}

	function dbactionUpdate($subaction)
	{
		$mods = array('xcache', 'suhosin', 'ioncube', 'zend');
/*
		foreach ($mods as &$m) {
			$v = "enable_{$m}_flag";
			$f = "enable_{$m}.flg";

			if ($this->main->phpini_flag_b->$v === 'on') {
				exec("echo '' > /usr/local/lxlabs/kloxo/etc/flag/{$f}");
			} else {
				exec("rm -rf /usr/local/lxlabs/kloxo/etc/flag/{$f}");
			}
		}

		$this->setInitString();
*/

		$this->createIniFile();
	}

}
