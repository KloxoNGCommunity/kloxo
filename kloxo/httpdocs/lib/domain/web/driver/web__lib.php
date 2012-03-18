<?php

class web__ extends lxDriverClass
{
	function __construct()
	{
	}

	static function uninstallMeTrue($drivertype = null)
	{
		$list = getWebDriverList($drivertype);

		$l = $list[0];

		if ($l === 'apache') { $l = 'httpd'; }

		lxshell_return("service", $l, "stop");
		lxshell_return("rpm", "-e", "--nodeps", $l);

		lxshell_return("chkconfig", $l, "off");

		if (file_exists("/etc/init.d/{$l}")) {
			lunlink("/etc/init.d/{$l}");
		}
	}

	static function installMeTrue($drivertype = null)
	{
		$list = getWebDriverList($drivertype);

		$isproxyorapache = isWebProxyOrApache($drivertype);

		if (!$isproxyorapache) {
			self::uninstallMeTrue('apache');
		}

		foreach ($list as &$l) {
			$a = ($l === 'apache') ? 'httpd' : $l;

			if ($a === 'httpd') {
				$ret = lxshell_return("yum", "-y", "install", $a, "mod_ssl", "mod_rpaf");
				lxfile_cp(getLinkCustomfile("/usr/local/lxlabs/kloxo/file/apache", "rpaf.conf"),
						"/etc/httpd/conf.d/rpaf.conf");
				lxfile_cp(getLinkCustomfile("/usr/local/lxlabs/kloxo/file/apache", "ssl.conf"),
						"/etc/httpd/conf.d/ssl.conf");
			}
			elseif ($a === 'lighttpd') {
				$ret = lxshell_return("yum", "-y", "install", $a, "lighttpd-fastcgi");
			}
			elseif ($a === 'nginx') {
				$ret = lxshell_return("yum", "-y", "install", $a);
			}

			if ($ret) {
				throw new lxException('install {$a} failed', 'parent');
			}

			lxshell_return("chkconfig", $a, "on");

			setCopyWebConfFiles($l);

			createRestartFile($l);
		}

		$ret = lxshell_return("yum", "-y", "install", "php-fpm");
		lxfile_cp(getLinkCustomfile("/usr/local/lxlabs/kloxo/file/php-fpm", "php-fpm.conf"),
				"/etc/php-fpm.conf");

		lxfile_cp(getLinkCustomfile("/usr/local/lxlabs/kloxo/file/php-fpm", "php-fpm.conf.tpl"),
				"/home/php-fpm/tpl/php-fpm.conf.tpl");

		lxshell_return("chkconfig", "php-fpm", "on");
	}

	function createConffile()
	{
		$this->clearDomainIpAddress();

		$input = array();

		$input['domainname'] = $this->getDomainname();
		$input['user'] = $this->getUser();

		if ((!$input['domainname']) || (!$input['user'])) {
			return null;
		}

		$input['wildcards'] = $this->isWildcards();
		$input['indexorder'] = $this->getIndexFileOrder();
		$input['parkdomains'] = $this->getParkDomains();
		$input['serveraliases'] = $this->getServerAliases();
		$input['dirprotect'] = $this->getDirprotect();
		$input['iplist'] = $this->getAllIps();
		$input['ipssllist'] = $this->getSslIpList();
		$input['stats'] = $this->getStats();
		$input['wwwredirect'] = $this->getWwwRedirect();
		$input['domainredirect'] = $this->getRedirectDomains();

		$input['apacheextratext'] = $this->getApacheExtraText();
		$input['lighttpdextratext'] = $this->getLighttpdExtraText();
		$input['nginxextratext'] = $this->getNginxExtraText();

		$input['disabled'] = $this->getDisabled();
		$input['extrabasedir'] = $this->AddExtraBaseDir();
		$input['blockips'] = $this->getBlockIP();

		$input['webmailremote'] = $this->getWebmailInfo('remote');
		$input['webmailapp'] = $this->getWebmailInfo('app');
		$input['webmailappdefault'] = self::getWebmailAppDefault();

		$input['rootpath'] = $this->main->getFullDocRoot();
		$input['disablephp'] = $this->disablePhp();

		$input['phpcgitype'] = $this->getPhpCgiType();
		$input['fastcgichildren'] = $this->getFastcgiChildren();
		$input['fastcgimaxprocs'] = $this->getFastcgiMaxProcs();

		$input['setdomains'] = true;

		self::setCreateConfFile($input);

		$this->setLogfile();
	}

// MR -- (1) related to create conf file

	function updateMainConfFile()
	{
		$input = array();

		$input['setdefaults'] = 'init';
		$input['iplist'] = self::getAllIps();

		$input['indexorder'] = self::getIndexFileOrderDefault();

		self::setCreateConfFile($input);
	}

	function createSSlConf()
	{
		$input = array();

		$input['iplist'] = self::getAllIps();

		$ipssllist = ($this->main->__var_ipssllist) ? $this->main->__var_ipssllist : null;

		if ($ipssllist) {
			$clist = null;

			foreach ((array)$ipssllist as $ip) {
				$clist[] = array('ip' => $ip['ipaddr'], 'cert' => sslcert::getSslCertnameFromIP($ip['nname']));
			}

			$input['certlist'] = $clist;

			$input['setdefaults'] = 'ssl';

			$input['indexorder'] = self::getIndexFileOrderDefault();
			
			$input['userlist'] = $this->getUserList();

			self::setCreateConfFile($input);
		}
	}

	function createCpConfig()
	{
		$list = array('default', 'cp', 'disable');

		$input = array();
		
		$input['iplist'] = self::getAllIps();

		$input['indexorder'] = self::getIndexFileOrderDefault();

		foreach ($list as &$v) {
			$input['setdefaults'] = $v;
			
			if ($v === 'default') {
				$input['userlist'] = $this->getUserList();			
			}

			self::setCreateConfFile($input);
		}
	}

	static function createWebDefaultConfig()
	{
		$input = array();

		$input['setdefaults'] = 'webmail';

		$input['iplist'] = self::getAllIps();

		$input['webmailappdefault'] = self::getWebmailAppDefault();

		$input['indexorder'] = self::getIndexFileOrderDefault();

		self::setCreateConfFile($input);
	}

	static function getWebmailAppDefault()
	{
		global $login;

		$webmailapp = $login->getObject('general')->generalmisc_b->webmail_system_default;

		if (($webmailapp === '--chooser--') || (!isset($webmailapp))) {
			$ret = null;
		} else {
			$ret = $webmailapp;
		}

		return $ret;
	}

// MR -- (2) call by 'related to create conf file' (1)

	function getDomainname()
	{
		$ret = (isset($this->main->nname)) ? $this->main->nname : null;

		return $ret;
	}

	function getUser()
	{
		$ret = (isset($this->main->customer_name)) ? $this->main->customer_name : null;

		return $ret;
	}
	
	function getUserList()
	{
		// MR -- look like not work calling $this->main->__var_clientlist
		// so, taken from database directly

		$string = "syncserver = '{$this->main->__syncserver}'";
		$clientdb = new Sqlite(null, 'client');
		$ulist = $clientdb->getRowsWhere($string, array('nname'));

		$users = array();

		foreach ($ulist as $u) {
			$users[] = $u['nname'];
		}
		
		return $users;
	}

	static function getAllIps()
	{
		$iplist = os_get_allips();

		$list = array();

		foreach ($iplist as $ip) {
			$list[] = $ip;
		}

		return $list;
	}

	function getWebmailInfo($for)
	{
		$domainname = $this->getDomainname();

		// MR -- look like not work calling $this->main->__var_mmaillist
		// so, taken from database directly

		$string = "syncserver = '{$this->main->__syncserver}'";
		$mmaildb = new Sqlite(null, 'mmail');
		$mlist = $mmaildb->getRowsWhere($string, array('nname', 'parent_clname', 'webmailprog', 'webmail_url', 'remotelocalflag'));

		if ($mlist) {
			foreach ($mlist as $m) {
				if ($m['nname'] === $domainname) {
					$list = $m;
					break;
				}
			}
		} else {
			// --- for the first time domain create
			if (!isset($list)) {
				$list = array('nname' => $domainname, 'parent_clname' => 'domain-'.$domainname, 
						'webmailprog' => '', 'webmail_url' => '', 'remotelocalflag' => 'local');
			}
		}

		$r = null;

		if ($for === 'remote') {
			if ($list['remotelocalflag'] === 'remote') {
				$r = $list['webmail_url'];
			}

		} elseif ($for === 'app') {
			if ($list['remotelocalflag'] !== 'remote') {
				if ($list['webmailprog'] === '--system-default--') {
					$r = self::getWebmailAppDefault();
				} else {
					$r = $list['webmailprog'];
				}
			}
		}

		return $r;
	}

	static function setCreateConfFile($input)
	{
		$conffile = $conftpl = $conftype = null;

		if (array_key_exists('setdefaults', $input)) {
			$conftype = 'defaults';

			if ($input['setdefaults'] === 'ssl') {
				$conffile = '__ssl.conf';
			} elseif ($input['setdefaults'] === 'default') {
				$conffile = '_default.conf';
			} else {
				$conffile = "{$input['setdefaults']}.conf";
			}

			$conftpl = 'defaults';
		} else {
			if (array_key_exists('setdomains', $input)) {
				$conftype = 'domains';

				$conftpl = 'domains';

				$conffile = $input['domainname'] . '.conf';
			}
		}

		$input['reverseproxy'] = isWebProxy();

		$list = getWebDriverList();

		foreach ($list as &$l) {
			$tplsource = getLinkCustomfile("/home/{$l}/tpl", "{$conftpl}.conf.tpl");

			$tpltarget = "/home/{$l}/conf/{$conftype}";

			$tpl = file_get_contents($tplsource);

			$tplparse = getParseInlinePhp($tpl, $input);

			file_put_contents("{$tpltarget}/{$conffile}", $tplparse);

			createRestartFile($l);

			if ($l === 'lighttpd') {
				self::setLighttpdPerlSuexec($input);
			}
		}
	}

	function AddExtraBaseDir()
	{
		$extrabasedir = $this->main->__var_extrabasedir;

		return trim($extrabasedir);
	}

	function getBlockIP()
	{
		$tblockip = $this->main->text_blockip;

		$t = trimSpaces($tblockip);
		$t = trim($t);

		if ($t) {
			$t = str_replace(".*", "", $t);
			$t = explode(" ", $t);
		} else {
			$t = null;
		}

		return $t;
	}

	function disablePhp()
	{
		$this->setPhpIni();

		$ret = ($this->main->priv->isOn('php_flag')) ? false : true;

		return $ret;
	}

	function getPhpCgiType()
	{
		$ret = ($this->main->priv->isOn('phpfcgi_flag')) ? 'fastcgi' : 'suexec';

		return $ret;
	}

	function getFastcgiMaxProcs()
	{
		$n = ($this->main->priv->phpfcgiprocess_num) ?
				$this->main->priv->phpfcgiprocess_num : "0";

		if (($n === 'Unlimited') || ($n === '-')) {
			$n = '0';
		}

		$ret = ($this->main->isOn('fcgi_children')) ? "1" : $n;

		return $ret;
	}

	function getFastcgiChildren()
	{
		$n = ($this->main->priv->phpfcgiprocess_num) ?
				$this->main->priv->phpfcgiprocess_num : "0";

		if (($n === 'Unlimited') || ($n === '-')) {
			$n = '0';
		}

		$ret = ($this->main->isOn('fcgi_children')) ? $n : "1";

		return $ret;
	}

	function setFixPhpFpm_nolog()
	{
		$this->setFixPhpFpm($nolog = true);
	}

	function setFixPhpFpm($nolog = null)
	{
		global $login;

		$login->loadAllObjects('client');
		$list = $login->getList('client');

		$tplsource = getLinkCustomfile("/home/php-fpm/tpl", "php-fpm.conf.tpl");

		$tpltarget = "/etc/php-fpm.conf";

		$tpl = file_get_contents($tplsource);

		$input = array();

		log_cleanup("Fixing Php-fpm config", $nolog);

		foreach ($list as $c) {
			$input['userlist'][] = $c->nname;
			log_cleanup("- set pool for '{$c->nname}'", $nolog);
		}

		$tplparse = getParseInlinePhp($tpl, $input);

		file_put_contents($tpltarget, $tplparse);

		createRestartFile('php-fpm');
	}

	function setFixChownChmodAll_nolog()
	{
		$this->setFixChownChmod('all', $nolog = true);
	}

	function setFixChownChmodOwn_nolog()
	{
		$this->setFixChownChmod('chown', $nolog = true);
	}

	function setFixChownChmodMod_nolog()
	{
		$this->setFixChownChmod('chmod', $nolog = true);
	}

	function setFixChownChmodAll()
	{
		$this->setFixChownChmod('all');
	}

	function setFixChownChmodOwn()
	{
		$this->setFixChownChmod('chown');
	}

	function setFixChownChmodMod()
	{
		$this->setFixChownChmod('chmod');
	}

	function setFixChownChmod($select, $nolog = null)
	{
		global $login;

		$login->loadAllObjects('client');
		$list = $login->getList('client');

		$userdirchmod = '751'; // need to change to 751 for nginx-proxy
		$phpfilechmod = '644';
		$domdirchmod = '755';

		// --- for /home/kloxo/httpd dirs (defaults pages)

		log_cleanup("Fix file permission problems for defaults pages (chown/chmod files)", $nolog);

		setKloxoHttpdChownChmod($nolog);
		setWebDriverChownChmod('apache', $nolog);
		setWebDriverChownChmod('lighttpd', $nolog);
		setWebDriverChownChmod('nginx', $nolog);

		// --- for domain dirs

		foreach($list as $c) {
			$clname = $c->getPathFromName('nname');
			$cdir = "/home/{$clname}";
			$dlist = $c->getList('domaina');
			$ks = "kloxoscript";

			exec("chown {$clname}:apache {$cdir}/");
			log_cleanup("- chown {$clname}:apache FOR {$cdir}/", $nolog);

			exec("chmod {$userdirchmod} {$cdir}/");
			log_cleanup("- chmod {$userdirchmod} FOR {$cdir}/", $nolog);

			exec("chown -R {$clname}:{$clname} {$cdir}/{$ks}/");
			log_cleanup("- chown {$clname}:{$clname} FOR INSIDE {$cdir}/{$ks}/", $nolog);

			exec("chown {$clname}:apache {$cdir}/{$ks}/");
			log_cleanup("- chown {$clname}:apache FOR {$cdir}/{$ks}/", $nolog);

			exec("find {$cdir}/{$ks}/ -type f -name \"*.php*\" " .
					"-exec chmod {$phpfilechmod} \{\} \\;");
			log_cleanup("- chmod {$phpfilechmod} FOR *.php* INSIDE {$cdir}/{$ks}/", $nolog);

			exec("find {$cdir}/{$ks}/ -type d " .
					"-exec chmod {$domdirchmod} \{\} \\;");
			log_cleanup("- chmod {$domdirchmod} FOR {$cdir}/{$ks}/ AND INSIDE", $nolog);

			foreach((array) $dlist as $l) {
				$web = $l->nname;

				if (($select === "all") || ($select === 'chown')) {
					exec("chown -R {$clname}:{$clname} {$cdir}/{$web}/");
					log_cleanup("- chown {$clname}:{$clname} FOR INSIDE {$cdir}/{$web}/", $nolog);
				}

				if (($select === "all") || ($select === 'chmod')) {
					exec("find {$cdir}/{$web}/ -type f -name \"*.php*\" " .
							"-exec chmod {$phpfilechmod} \{\} \\;");
					log_cleanup("- chmod {$phpfilechmod} FOR *.php* INSIDE {$cdir}/{$web}/", $nolog);

					exec("find {$cdir}/{$web}/ -type d " .
							"-exec chmod {$domdirchmod} \{\} \\;");
					log_cleanup("- chmod {$domdirchmod} FOR {$cdir}/{$web}/ AND INSIDE", $nolog);
				}

				exec("chown {$clname}:apache {$cdir}/{$web}/");
				log_cleanup("- chown {$clname}:apache FOR {$cdir}/{$web}/", $nolog);

				exec("chmod -R {$domdirchmod} {$cdir}/{$web}/cgi-bin");
				log_cleanup("- chmod {$domdirchmod} FOR {$cdir}/{$web}/cgi-bin AND FILES", $nolog);
			}
		}
	}

	function getSslIpList()
	{
		$domainname = $this->getDomainname();

		$domainipaddress = (isset($this->main->__var_domainipaddress)) ?
				$this->main->__var_domainipaddress : null;

		if ($domainipaddress) {
			$list = array();

			foreach ($domainipaddress as &$dom) {
				if ($dom === $domainname) {
					$list[] = $dom;
				}
			}

			$ret = $list;
		} else {
			$ret = null;
		}

		return $ret;
	}

	function getDirprotect()
	{
		$dirprotect = (isset($this->main->__var_dirprotect)) ?
				$this->main->__var_dirprotect : null;

		if ($dirprotect) {
			$input = array();

			foreach ((array)$dirprotect as $prot) {
				if (!$prot->isOn('status') || $prot->isDeleted()) {
					continue;
				}

				$input[] = array('authname' => $prot->authname,
						'path' => $prot->path, 'file' => $prot->getFileName());
			}

			return $input;
		} else {
			return null;
		}
	}

	function getIndexFileOrder()
	{
		if ($this->main->indexfile_list) {
			$list = $this->main->indexfile_list;
		} else {
		//	$list = $this->main->__var_index_list;
			$list = self::getIndexFileOrderDefault();
		}

		if ($list) {
			$string = $list;
		} else {
			$string = null;
		}

		return $string;
	}

	static function getIndexFileOrderDefault()
	{
    		return array('index.php', 'index.html', 'index.shtml', 'index.htm', 
			'default.htm', 'Default.aspx', 'Default.asp', 'index.pl');

	}
	function isWildcards()
	{
		$serveralias = (isset($this->main->server_alias_a)) ?
				$this->main->server_alias_a : null;

		foreach ($serveralias as $val) {
			if ($val->nname === '*') {
				return true;
			}
		}

		return false;
	}

	function getServerAliases()
	{
		$serveralias = (isset($this->main->server_alias_a)) ?
				$this->main->server_alias_a : null;

		if ($serveralias) {
			$list = array();

			foreach ($serveralias as $val) {
				if ($val->nname !== '*') {
					$list[] = $val->nname . '.' . $this->getDomainname();
				}
			}
		} else {
			$list = null;
		}

		return $list;
	}

	function getParkDomains()
	{
		$addonlist = (isset($this->main->__var_addonlist)) ?
				$this->main->__var_addonlist : null;

		if ($addonlist) {
			$list = array();

			foreach ((array)$addonlist as $val) {
				if ($val->ttype === 'redirect') {
					continue;
				}

				$list[] = array('parkdomain' => $val->nname, 'mailflag' => $val->mail_flag);
			}
		} else {
			$list = null;
		}

		return $list;
	}

	function getRedirectDomains()
	{
		$addonlist = (isset($this->main->__var_addonlist)) ? $this->main->__var_addonlist : null;

		if ($addonlist) {
			$list = array();

			foreach ((array)$addonlist as $val) {
				if ($val->ttype !== 'redirect') {
					continue;
				}

				$list[] = array('redirdomain' => $val->nname, 'redirpath' => $val->destinationdir,
						'mailflag' => $val->mail_flag);
			}
		} else {
			$list = null;
		}

		return $list;
	}

	function getStats()
	{
		$prog = ($this->main->__var_statsprog) ? $this->main->__var_statsprog : 'awstats';

		$prot = ($this->main->stats_password) ? $this->main->stats_password : null;

		web::createstatsConf($this->main->nname, $this->main->stats_username, $this->main->stats_password);

		return array('app' => $prog, 'protect' => $prot);
	}

	function getWwwRedirect()
	{
		if ($this->main->isOn('force_www_redirect')) {
			$ret = true;
		} else {
			$ret = false;
		}

		return $ret;
	}

	function getApacheExtraText()
	{
		if ($this->main->text_extra_tag) {
			$ret = $this->main->text_extra_tag;
		} else {
			$ret = null;
		}

		return $ret;
	}

	function getLighttpdExtraText()
	{
		$ret = null;

		if ($this->main->text_lighty_rewrite) {
			$ret .= $this->main->text_lighty_rewrite;
		}

	/*
		if ($this->main->customerror_b->url_404) {
			$ret .= $this->main->customerror_b->url_404;
		}
	*/
		return $ret;
	}

	function getNginxExtraText()
	{
		return null;
	}

	function getDisabled()
	{
		if ($this->main->isOn('status')) {
			$ret = false;
		} else {
			$ret = true;
		}

		return $ret;
	}

	static function setLighttpdPerlSuexec($input)
	{
		$tplsource = "/home/lighttpd/tpl/perlsuexec.sh.tpl";

		$tpltarget = "/home/httpd/{$input['domainname']}/perlsuexec.sh";

		$tpl = file_get_contents($tplsource);

		$tplparse = getParseInlinePhp($tpl, $input);

		file_put_contents($tpltarget, $tplparse);

		lxfile_unix_chmod($tpltarget, '755');
	}

// MR -- (3) target to .httaccess or php.ini or log

	function setLogfile()
	{
		global $sgbl;

		$domainname = $this->main->nname;

		$web_home = $sgbl->__path_httpd_root;
		$log_path = "{$web_home}/{$domainname}/stats";
		$cust_log = "{$log_path}/{$domainname}-custom_log";
		$err_log = "{$log_path}/{$domainname}-error_log";

		// MR -- back to original!
/*
		if (file_exists($cust_log)) {
			lxfile_cp($cust_log, "{$log_path}/custom.log");
		}

		if (file_exists($err_log)) {
			lxfile_cp($err_log, "{$log_path}/error.log");
		}
*/
	}

	function setPhpIni()
	{
		$domainname = $this->main->nname;
		$username = $this->main->username;

		lxfile_unix_chown("/home/httpd/{$domainname}", "{$username}:apache");
		lxfile_unix_chmod("/home/httpd/{$domainname}", "0775");

		if (!lxfile_exists("/home/httpd/{$domainname}/php.ini")) {
			// MR -- issue #650 - lxuser_cp does not work and change to lxfile_cp;
			// lighttpd use lxfile_cp
			lxfile_cp("/etc/php.ini", "/home/httpd/{$domainname}/php.ini");
		}
	}

	function createHotlinkHtaccess()
	{
		$string = $this->hotlink_protection();
		$stlist[] = "### Kloxo Hotlink Protection";
		$endlist[] = "### End Kloxo Hotlink Protection";
		$startstring = $stlist[0];
		$endstring = $endlist[0];
		$htfile = "{$this->main->getFullDocRoot()}/.htaccess";

		file_put_between_comments($this->getUser(), $stlist, $endlist,
				$startstring, $endstring, $htfile, $string);

		//	$this->norestart = 'on';
	}

	function hotlink_protection()
	{
		if (!$this->main->isOn('hotlink_flag')) {
			return null;
		}

		$allowed_domain_string = $this->main->text_hotlink_allowed;
		$allowed_domain_string = trim($allowed_domain_string);
		$allowed_domain_string = str_replace("\r", "", $allowed_domain_string);
		$allowed_domain_list = explode("\n", $allowed_domain_string);

		$string = null;
		$string .= "\tRewriteEngine on\n";
		$string .= "\tRewriteCond %{HTTP_REFERER} !^$\n";

		$ht = trim($this->main->hotlink_redirect, "/");
		$ht = "/$ht";

		foreach ($allowed_domain_list as $al) {
			$al = trim($al);

			if (!$al) {
				continue;
			}
			$string .= "\tRewriteCond %{HTTP_REFERER} !^http://.*{$al}.*$ [NC]\n";
			$string .= "\tRewriteCond %{HTTP_REFERER} !^https://.*{$al}.*$ [NC]\n";
		}

		$l = $this->main->nname;

		$string .= "\tRewriteCond %{HTTP_REFERER} !^http://.*{$l}.*$ [NC]\n";
		$string .= "\tRewriteCond %{HTTP_REFERER} !^https://.*{$l}.*$ [NC]\n";
		$string .= "\tRewriteRule .*[JrRjP][PpdDAa][GfFgrR]$|.*[Gg][Ii][Ff]$ {$ht} [L]\n";

		return $string;
	}

// MR -- (4) call to 'related to create conf file' and others

	function clearDomainIpAddress()
	{
		$iplist = os_get_allips();

		foreach ($this->main->__var_domainipaddress as $ip => $dom) {
			if (!array_search_bool($ip, $iplist)) {
				unset($this->main->__var_domainipaddress[$ip]);
			}
		}
	}

	function delDomain()
	{
		$domainname = $this->getDomainname();

		// Very important. If the nname is null,
		// then the 'rm - rf' command will delete all the domains.
		// So please be careful here. Must find a better way to delete stuff.
		if (!$domainname) {	return null;	}

		$list = getWebDriverList();

		foreach ($list as &$l) {
			$p = "/home/{$l}/conf/domains";

			lxfile_rm("{$p}/{$domainname}.conf");
		}

		$this->main->deleteDir();

		$this->createSSlConf();

		// MR -- relate to fixed ip/~client, but better on add/delete client process
		// meanwhile enough in here

		$this->createCpConfig();
	}

	function addDomain()
	{
		$this->main->createDir();

		$this->createConffile();

		$this->main->createPhpInfo();

		$this->createSSlConf();

		// MR -- relate to fixed ip/~client, but better on add/delete client process
		// meanwhile enough in here

		$this->createCpConfig();
	}

	function dbactionAdd()
	{
		$this->addDomain();

		$this->main->doStatsPageProtection();
	}

	function dbactionDelete()
	{
		$this->delDomain();
	}

	function dosyncToSystemPost()
	{
		if (!$this->isOn('norestart')) {
			createRestartFile("apache");
		}
	}

	function fullUpdate()
	{
		global $sgbl;

		$domname = $this->getDomainname();
		$uname = $this->getUser();

		$hroot = $sgbl->__path_httpd_root;
		$droot = $this->main->getFullDocRoot();

		lxfile_mkdir("{$hroot}/{$domname}/webstats");

		$this->main->createPhpInfo();
		web::createstatsConf($domname, $this->main->stats_username, $this->main->stats_password);

		//	$this->createSSlConf();

		$this->createConffile();

		lxfile_unix_chown_rec("{$droot}/", "{$uname}:{$uname}");
		lxfile_unix_chmod("{$droot}/", "0755");
		lxfile_unix_chmod("{$droot}", "0755");
		lxfile_unix_chown("{$hroot}/{$domname}", "{$uname}:apache");
	}

	function dbactionUpdate($subaction)
	{
		$domname = $this->getDomainname();
		$uname = $this->getUser();

		$ret = null;

		if (!$uname) {
			log_log("critical", "Lack customername for web: {$domname}");

			return $ret;
		} else {
			switch ($subaction) {
				case "full_update":
					$this->fullUpdate();
					$this->main->doStatsPageProtection();
					break;

				case "changeowner":
					$this->main->webChangeOwner();
					$this->createConffile();
					break;

				case "create_config":
				case "addondomain":
					$this->createConffile();
					break;

				case "add_delete_dirprotect":
				case "extra_tag" :
				case "add_dirprotect" :
				case "custom_error":
				case "dirindex":
				case "docroot":
				case "ipaddress":
				case "blockip";
				case "add_redirect_a":
				case "delete_redirect_a":
				case "delete_redirect_a":
				case "add_webindexdir_a":
				case "delete_webindexdir_a":
				case "add_server_alias_a" :
				case "delete_server_alias_a" :
				case "configure_misc":
					$this->createConffile();
					break;

				case "redirect_domain" :
					$this->createConffile();
					break;

				case "fixipdomain":
					$this->createConffile();
					$this->updateMainConfFile();
					$this->createSSlConf();
					break;

				case "enable_php_manage_flag":
					$this->createConffile();
					$this->updateMainConfFile();
					break;

				case "toggle_status" :
					$this->createConffile();
					break;

				case "hotlink_protection":
					$this->createHotlinkHtaccess();
					break;

				case "enable_php_flag":
				case "enable_cgi_flag":
				case "enable_inc_flag":
				case "enable_ssl_flag" :
					$this->createConffile();
					break;

				case "stats_protect":
					$this->main->doStatsPageProtection();
					$this->createConffile();
					break;

				case "default_domain":
					$this->main->setupDefaultDomain();
					break;

				case "graph_webtraffic":
					return rrd_graph_single("webtraffic (bytes)", $this->main->nname,
							$this->main->rrdtime);
					break;

				case "run_stats":
					$this->main->runStats();
					break;

				case "static_config_update":
					$this->updateMainConfFile();
					$this->createSSlConf();
					$this->createCpConfig();
					self::createWebDefaultConfig();
					break;

				case "fix_phpfpm":
					$this->setFixPhpFpm();
					break;

				case "fix_phpfpm_nolog":
					$this->setFixPhpFpm_nolog();
					break;

				case "fix_chownchmod_all":
					$this->setFixChownChmodAll();
					break;

				case "fix_chownchmod_own":
					$this->setFixChownChmodOwn();
					break;

				case "fix_chownchmod_mod":
					$this->setFixChownChmodMod();
					break;

				case "fix_chownchmod_all_nolog":
					$this->setFixChownChmodAll_nolog();
					break;

				case "fix_chownchmod_own_nolog":
					$this->setFixChownChmodOwn_nolog();
					break;

				case "fix_chownchmod_mod_nolog":
					$this->setFixChownChmodMod_nolog();
					break;
			}
		}
	}

	function do_backup()
	{
		return $this->main->do_backup();
	}

	function do_restore($docd)
	{
		global $sgbl;

		$uname = $this->getUser();

		$fullpath = "{$sgbl->__path_customer_root}/{$uname}/";

		$this->main->do_restore($docd);

		lxfile_unix_chown_rec($fullpath, $uname);
	}
}
