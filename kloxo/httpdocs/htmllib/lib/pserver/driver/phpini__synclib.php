<?php 

class phpini__sync extends Lxdriverclass {


function initString($ver)
{
	$pclass = $this->main->getParentClass();

	$this->main->fixphpIniFlag();

	$phpver = getPhpVersion();
	$phpbranch = getPhpBranch();

	// MR -- for zend
	if (version_compare($phpver, "5.3.0", ">=")) {
		$list = array("{$phpbranch}-zend-guard-loader", "{$phpbranch}-zend-guard",
			"php-zend-guard-loader", "php-zend-guard");
	} else {
		$list = array("{$phpbranch}-zend-optimizer-loader", "{$phpbranch}-zend-optimizer",
			"php-zend-optimizer", "php-zend");
	}

	$installed = false;

	foreach ($list as &$l) {
		$installed = isRpmInstalled($l);
		if ($installed) {
			$mod = $l;
			break; 
		}
	}

	if ($this->main->phpini_flag_b->isON('enable_zend_flag')) {
		if (!installed) {
			foreach ($list as &$l) {
				$f = "/home/rpms/{$l}-*.rpm";
				$flist = glob($f);

				if ($flist) {
					$ret = lxshell_return("rpm", "-ivh", "--replacefiles", $f);
				} else {
					$ret = lxshell_return("yum", "-y", "install", $r);
				}

				if (!ret) { break; }
			}
		}
	} else {
		if (installed) {
			$ret = lxshell_return("rpm', "-e', "--nodeps", "{$mod}");

			if ($ret) {
				throw new lxException("remove {$mod} failed", 'parent');
			}
		}
	}

	// MR -- for xcache
	$list = array("{$phpbranch}-xcache", "php-xcache");

	$installed = false;

	foreach ($list as &$l) {
		$installed = isRpmInstalled($l);
		if ($installed) {
			$mod = $l;
			break; 
		}
	}

	if ($this->main->phpini_flag_b->isON('enable_xcache_flag')) {
		if (!installed) {
			foreach ($list as &$l) {
				$f = "/home/rpms/{$l}-*.rpm";
				$flist = glob($f);

				if ($flist) {
					$ret = lxshell_return("rpm", "-ivh", "--replacefiles", $f);
				} else {
					$ret = lxshell_return("yum", "-y", "install", $r);
				}

				if (!ret) { break; }
			}
		}
	} else {
		if (installed) {
			$ret = lxshell_return("rpm', "-e', "--nodeps", "{$mod}");

			if ($ret) {
				throw new lxException("remove {$mod} failed", 'parent');
			}
		}
	}

	// MR -- for ioncube
	$list = array("{$phpbranch}-ioncube-loader", "php-ioncube-loader", "php-ioncube");

	$installed = false;

	foreach ($list as &$l) {
		$installed = isRpmInstalled($l);

		if ($installed) {
			$mod = $l;
			break; 
		}
	}

	if ($this->main->phpini_flag_b->isON('enable_ioncube_flag')) {
		if (!installed) {
			foreach ($list as &$l) {
				$f = "/home/rpms/{$l}-*.rpm";
				$flist = glob($f);

				if ($flist) {
					$ret = lxshell_return("rpm", "-ivh", "--replacefiles", $f);
				} else {
					$ret = lxshell_return("yum", "-y", "install", $r);
				}

				if (!ret) { break; }
			}
		}
	} else {
		if (installed) {
			$ret = lxshell_return("rpm', "-e', "--nodeps", "{$mod}");

			if ($ret) {
				throw new lxException("remove {$mod} failed", 'parent');
			}
		}
	}

	// MR -- for suhosin
	$list = array("{$phpbranch}-suhosin", "php-suhosin");

	$installed = false;

	foreach ($list as &$l) {
		$installed = isRpmInstalled($l);

		if ($installed) {
			$mod = $l;
			break; 
		}
	}

	if ($this->main->phpini_flag_b->isON('enable_suhosin_flag')) {
		if (!installed) {
			foreach ($list as &$l) {
				$f = "/home/rpms/{$l}-*.rpm";
				$flist = glob($f);

				if ($flist) {
					$ret = lxshell_return("rpm", "-ivh", "--replacefiles", $f);
				} else {
					$ret = lxshell_return("yum", "-y", "install", $r);
				}

				if (!ret) { break; }
			}
		}
	} else {
		if (installed) {
			$ret = lxshell_return("rpm', "-e', "--nodeps", "{$mod}");

			if ($ret) {
				throw new lxException("remove {$mod} failed", 'parent');
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

	$ver = find_php_version();

	$this->initString($ver);

	$l1 = $this->main->getInheritedList();
	$l2 = $this->main->getLocalList();
	$l3 = $this->main->getExtraList();

	$ll  = lx_array_merge(array($l1, $l2, $l3));
	$list =  array_unique($ll);

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
		$elogfile = "/home/{$this->main->__var_customer_name}/__processed_stats/{$this->main->getParentName()}.phplog";

		$ptarget = "$sgbl->__path_httpd_root/{$this->main->getParentName()}/php.ini";
		file_put_contents($ptarget, $pparse);

		$htfile = "{$this->main->__var_docrootpath}/.htaccess";		
		$ht1file = "/home/{$this->main->__var_customer_name}/kloxoscript/.htaccess";

		file_put_between_comments("{$this->main->__var_web_user}:apache", $stlist, $endlist, $startstring, $endstring, $htfile, $hparse);
		file_put_between_comments("{$this->main->__var_web_user}:apache", $stlist, $endlist, $startstring, $endstring, $ht1file, $hparse);

		lxfile_unix_chown($htfile, "{$this->main->__var_web_user}:apache");

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
	$this->createIniFile();
}

}
