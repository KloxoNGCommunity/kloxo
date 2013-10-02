<?php

class dns__ extends lxDriverClass
{
	function __construct()
	{
	}

	static function installMeTrue($drivertype = null)
	{
		self::setDnsserverInstall($drivertype);
		self::setBaseDnsConfig($drivertype);

		$altname = ($drivertype === 'bind') ? 'named' : $drivertype;

		lxshell_return("chkconfig", $altname, "on");

		if ($altname === 'djbdns') {
			lxshell_return("/etc/init.d/djbdns", "setup");
		}

		setCopyDnsConfFiles($drivertype);

		if ($drivertype === 'pdns') {
			PreparePowerdnsDb($nolog);
		} else {
			$hwcpath = "/home/{$drivertype}/conf";

			exec("rm -rf $hwcpath/master/*; rm -rf $hwcpath/reverse/*; rm -rf $hwcpath/slave/*");
		}

		createRestartFile($altname);
	}

	static function unInstallMeTrue($drivertype = null)
	{
		$altname = ($drivertype === 'bind') ? 'named' : $drivertype;

		lxshell_return("service", $altname, "stop");

		if ($drivertype === 'bind') {
			// using it because need bind-utils still exists
			setRpmRemoved($drivertype);
		} elseif ($drivertype === 'pdns') {
			setRpmRemovedViaYum($drivertype);
			setRpmRemovedViaYum($drivertype . "-backend-mysql");
			setRpmRemovedViaYum($drivertype . "-backend-geo");
		} else {
			setRpmRemovedViaYum($drivertype);
		}

		if (file_exists("/etc/init.d/{$altname}")) {
			lunlink("/etc/init.d/{$altname}");
		}
	}

	static function setDnsserverInstall($drivertype = null)
	{
		setRpmInstalled($drivertype);

		if ($drivertype === 'bind') {
			setRpmInstalled($drivertype . "-utils");
			setRpmRemoved("{$drivertype}-chroot");
		} elseif ($drivertype === 'pdns') {
			setRpmInstalled($drivertype . "-backend-mysql");
			setRpmInstalled($drivertype . "-backend-geo");
		}

		$altname = ($drivertype === 'bind') ? 'named' : $drivertype;

		$initfile = getLinkCustomfile("/home/{$drivertype}/etc/init.d", "$altname.init");

		if (file_exists($initfile)) {
			lxfile_cp($initfile, "/etc/init.d/{$altname}");
		}
	}

	static function setBaseDnsConfig($drivertype = null)
	{
	}

	function createConfFile($action = null)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$this->syncAddFile($this->main->nname, $action);
	
		foreach ((array)$this->main->__var_addonlist as $d) {
			$this->syncAddFile($d->nname, $action);
		}
	
	}

	function syncAddFile($domainname, $action = null)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$input = array();

		$input['domainname'] = $domainname;
		$input['ttl'] = $this->main->ttl;
		$input['nameduser'] = $sgbl->__var_programuser_dns;
		$input['soanameserver'] = $this->main->soanameserver;
		$input['email'] = $this->main->__var_email;
		$input['serial'] = $this->main->__var_ddate;
		$input['dns_records'] = $this->main->dns_record_a;

		// MR -- not work and not implementing yet!
	//	$input['account'] = $this->parent->getRealClientParentO()->getPathFromName();

		if ($action) {
			$input['action'] = $action;
		}

		self::setCreateConfFile($input);
	}

	static function setCreateConfFile($input)
	{
		$driverapp = slave_get_driver('dns');

		$altname = ($driverapp === 'bind') ? 'named' : $driverapp;

		if ($driverapp !== 'pdns') {
			$tplsource = getLinkCustomfile("/home/{$driverapp}/tpl", "domains.conf.tpl");
			$tpltarget = "/home/{$driverapp}/conf/master/" . $input['domainname'];

			$tpl = file_get_contents($tplsource);

			$tplparse = getParseInlinePhp($tpl, $input);

			if ($tplparse) {
				file_put_contents($tpltarget, $tplparse);
			}
		} else {
			if (!isset($input['action'])) {
				$input['action'] = 'add';
			}

			self::setPdnsSpecific($input);
		}
	}

	function syncCreateConf($action = null)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$driverapp = slave_get_driver('dns');

		$altname = ($driverapp === 'bind') ? 'named' : $driverapp;

		// MR -- powerdns no need it
		if ($driverapp === 'pdns') { return; }

		$input = array();

		$input['action'] = $action;

		$domains = array();

		if ($action === 'fix') {
			$domains[] = $this->main->nname;
	
			foreach ((array)$this->main->__var_addonlist as $d) {
				$domains[] = $d->nname;
			}

			$input['domains'] = $domains;
		} elseif ($action === 'update') {
			$input['domain'] = $this->main->nname;
		}

		$tplsource = getLinkCustomfile("/home/{$driverapp}/tpl", "list.master.conf.tpl");

		$tpl = file_get_contents($tplsource);

		$tplparse = getParseInlinePhp($tpl, $input);
	}

	function createAllowTransferIps()
	{
		global $gbl, $sgbl, $login, $ghtml;

		$driverapp = slave_get_driver('dns');

		$altname = ($driverapp === 'bind') ? 'named' : $driverapp;

		// MR -- powerdns also nsd no need it
		if ($driverapp === 'pdns') { return; }

		$input = array();

		$input['ip'] = $this->getIps();

		$tplsource = getLinkCustomfile("/home/{$driverapp}/tpl", "list.transfered.conf.tpl");

		$tpl = file_get_contents($tplsource);

		$tplparse = getParseInlinePhp($tpl, $input);
	}

	function getIps()
	{
		$nobase = true;

		$iplist = rl_exec_get('localhost', 'localhost', 'getIpfromARecord', array($nobase));

		return $iplist;
	}

	function dbactionAdd()
	{
		global $gbl, $sgbl, $login, $ghtml;

		$this->createConfFile();
		$this->createAllowTransferIps();
		$this->syncCreateConf();

	}

	function dbactionUpdate($subaction)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$driverapp = slave_get_driver('dns');

		$altname = ($driverapp === 'bind') ? 'named' : $driverapp;

		if ($driverapp === 'pdns') {
			$this->createConfFile('update');
		} else {
			switch ($subaction) {
				case "allowed_transfer":
					$this->createAllowTransferIps();

					break;
				case "synchronize":
					$this->syncCreateConf('update');

					break;
				case "synchronize_fix":
					$this->syncCreateConf('fix');

					break;
				case "domain":
					$this->createConfFile();

					break;
				case "full_update":
				default:
					$this->createConfFile();
					$this->createAllowTransferIps();
					$this->syncCreateConf('update');

					break;
			}
		}
	}

	function dbactionDelete()
	{
		global $gbl, $sgbl, $login, $ghtml;

		$driverapp = slave_get_driver('dns');

		$altname = ($driverapp === 'bind') ? 'named' : $driverapp;

		$domainname = $this->main->nname;

		if ($driverapp !== 'pdns') {
			$dnsfile = "/home/{$driverapp}/conf/master/{$domainname}";
			lxfile_rm($dnsfile);

			foreach ((array)$this->main->__var_addonlist as $d) {
				$addondomain = $d->nname;
				$dnsfile = "/home/{$driverapp}/conf/master/{$addondomain}";
				lxfile_rm($dnsfile);
			}

			$this->createAllowTransferIps();
			$this->syncCreateConf();

			if ($driverapp === 'bind') {
				exec("rndc reconfig");
			} elseif ($driverapp === 'nsd') {
				exec("nsdc rebuild");
			}
		} else {
			$input = array();

			$input['domainname'] = $domainname;

			$input['action'] = 'delete';

			self::setPdnsSpecific($input);

			foreach ((array)$this->main->__var_addonlist as $d) {
				$input = array();

				$input['domainname'] = $d->nname;

				$input['action'] = 'delete';

				$nameserver = null;

				foreach($this->main->dns_record_a as $dns) {
					if ($dns->ttype === "ns") {
						if (!$nameserver) {
							$nameserver = $dns->param;
						}
					}
				}

				if ($this->main->soanameserver) {
					$nameserver = $this->main->soanameserver;
				}

				$input['nameserver'] = $nameserver;

				self::setPdnsSpecific($input);
			}
		}
	}

	static function setPdnsSpecific($input)
	{
		$driverapp = slave_get_driver('dns');

		$input['rootpass'] = slave_get_db_pass();

		$tplsource = getLinkCustomfile("/home/{$driverapp}/tpl", "domains.conf.tpl");

		$tpl = file_get_contents($tplsource);

		$tplparse = getParseInlinePhp($tpl, $input);
	}

	function dosyncToSystemPost()
	{
		global $gbl, $sgbl, $login, $ghtml;

	//	$this->createAllowTransferIps();

		$driverapp = slave_get_driver('dns');

		if ($driverapp === 'bind') {
			// MR -- use 'rndc reconfig' for add/delete and 'rnd reload zone' for update
			// instead service restart; move to list.master.conf.tpl
		//	createRestartFile("named");
		} elseif ($driverapp === 'djbdns') {
		//	createRestartFile("djbdns");
		} elseif ($driverapp === 'maradns') {
		//	createRestartFile("maradns");
		} elseif ($driverapp === 'pdns') {
			// no need restart!
		} elseif ($driverapp === 'nsd') {
			// no need restart!
		}
	}
}