<?php 

class phpini__sync extends Lxdriverclass {


function initString($ver)
{
	$pclass = $this->main->getParentClass();

	$this->main->fixphpIniFlag();
/*
// MR -- xcache, zend and ioncube no setting inside php.ini on 6.2.x
// but directly install and that mean with their own ini

if ($this->main->phpini_flag_b->isON('enable_zend_flag')) {
	$this->main->phpini_flag_b->enable_zend_val =<<<XML
[Zend]
zend_extension_manager.optimizer=/usr/lib/kloxophp/zend/lib/Optimizer-3.2.8
zend_extension_manager.optimizer_ts=/usr/lib/kloxophp/zend/lib/Optimizer_TS-3.2.8
zend_optimizer.version=3.2.8
zend_extension=/usr/lib/kloxophp/zend/lib/ZendExtensionManager.so
zend_extension_ts=/usr/lib/kloxophp/zend/lib/ZendExtensionManager_TS.so
XML;

} else {
	$this->main->phpini_flag_b->enable_zend_val = "";
}

if ($this->main->phpini_flag_b->isON('enable_xcache_flag')) {
	lxfile_touch("../etc/flag/xcache_enabled.flg");
	$this->main->phpini_flag_b->enable_xcache_val =<<<XML
zend_extension = /usr/lib/php/modules/xcache.so
XML;
} else {
	lxfile_rm("../etc/flag/xcache_enabled.flg");
	$this->main->phpini_flag_b->enable_xcache_val = "";
}

if ($this->main->phpini_flag_b->isON('enable_ioncube_flag')) {
	$this->main->phpini_flag_b->enable_ioncube_val =<<<XML
zend_extension=/usr/lib/kloxophp/ioncube/ioncube_loader_lin_$ver.so
XML;
} else {
	$this->main->phpini_flag_b->enable_ioncube_val = "";
}
*/
}

function enableDisableModule($flag, $mod)
{
	// MR -- disable temporary until found better approach!

/*
	lxfile_rm("/etc/php.d/$mod.ini");
	lxfile_rm("/etc/php.d/$mod.noini");

	if ($this->main->phpini_flag_b->isOn($flag)) {
		lxfile_cp("../file/$mod.ini", "/etc/php.d/$mod.ini");
	} else {
		lxfile_cp("../file/$mod.ini", "/etc/php.d/$mod.noini");
	}
*/

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
