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
		}

		createRestartFile($altname);
	}

	static function unInstallMeTrue($drivertype = null)
	{
	//	setRpmRemoved($drivertype);

		setRpmRemovedViaYum($drivertype);

		if ($drivertype === 'bind') {
			setRpmRemovedViaYum($drivertype . "-libs");
		} elseif ($drivertype === 'pdns') {
			// MR -- look not work; backup via cleanup process
			setRpmRemovedViaYum($drivertype . "-backend-mysql");
			setRpmRemovedViaYum($drivertype . "-backend-geo");
		}

		$altname = ($drivertype === 'bind') ? 'named' : $drivertype;

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
			// MR -- look not work; backup via cleanup process
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

	function syncCreateConf()
	{
		global $gbl, $sgbl, $login, $ghtml;

		$driverapp = slave_get_driver('dns');

		$altname = ($driverapp === 'bind') ? 'named' : $driverapp;

		// MR -- powerdns no need it
		if ($driverapp === 'pdns') { return; }

		$input = array();

		$tplsource = getLinkCustomfile("/home/{$driverapp}/tpl", "list.master.conf.tpl");

		$tpl = file_get_contents($tplsource);

		$tplparse = getParseInlinePhp($tpl, $input);
	}

	function createAllowTransferIps()
	{
		global $gbl, $sgbl, $login, $ghtml;

		$driverapp = slave_get_driver('dns');

		$altname = ($driverapp === 'bind') ? 'named' : $driverapp;

		// MR -- powerdns no need it
		if ($driverapp === 'pdns') { return; }

		// MR -- maradns still using generic ('0.0.0.0')
		if ($driverapp === 'maradns') { return; }

		$input = array();

		$input['ip'] = $this->getIps();

		$tplsource = getLinkCustomfile("/home/{$driverapp}/tpl", "list.transfered.conf.tpl");

		$tpl = file_get_contents($tplsource);

		$tplparse = getParseInlinePhp($tpl, $input);
	}

	function getIps()
	{
		$iplist = rl_exec_get('localhost', 'localhost', 'getIpfromARecord', null);

		return $iplist;
	}

	function dbactionAdd()
	{
		$this->createConfFile();
		$this->syncCreateConf();
		$this->createAllowTransferIps();
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
					$this->syncCreateConf();

					break;
				case "domain":
					$this->createConfFile();

					break;
				case "full_update":
				default:
					$this->createConfFile();
					$this->syncCreateConf();
					$this->createAllowTransferIps();

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

			$this->syncCreateConf();
			$this->createAllowTransferIps();
		} else {
			$input = array();

			$input['domainname'] = $domainname;

			$input['action'] = 'delete';

			self::setPdnsSpecific($input);

			foreach ((array)$this->main->__var_addonlist as $d) {
				$input = array();

				$input['domainname'] = $d->nname;

				$input['action'] = 'delete';

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
			createRestartFile("named");
		} elseif ($driverapp === 'djbdns') {
			createRestartFile("djbdns");
		} elseif ($driverapp === 'pdns') {
			// no need restart!
		} elseif ($driverapp === 'maradns') {
			createRestartFile("maradns");
		}
	}
}