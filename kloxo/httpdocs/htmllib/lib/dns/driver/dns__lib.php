<?php

class dns__ extends lxDriverClass
{
	function __construct()
	{
	}

	static function installMeTrue($drivertype = null)
	{
		if ($drivertype === 'bind') {
			setRpmInstalled($drivertype . '-utils');

			setRpmRemoved($drivertype . '-chroot');
		}

		self::setDnsserverInstall($drivertype);
		self::setBaseDnsConfig($drivertype);

		$altname = ($drivertype === 'bind') ? 'named' : $drivertype;

		lxshell_return("chkconfig", $altname, "on");

		if ($altname === 'djbdns') {
			lxshell_return("/etc/init.d/djbdns", "setup");
		}

		setCopyDnsConfFiles($drivertype);

		createRestartFile($altname);
	}

	static function unInstallMeTrue($drivertype = null)
	{
		setRpmRemoved($drivertype);

		if ($drivertype === 'bind') {
			setRpmRemoved($drivertype . '-utils');

			setRpmRemoved($drivertype . '-chroot');
		}

		$altname = ($drivertype === 'bind') ? 'named' : $drivertype;

		if (file_exists("/etc/init.d/{$altname}")) {
			lunlink("/etc/init.d/{$altname}");
		}
	}

	static function setDnsserverInstall($drivertype = null)
	{
		setRpmInstalled($drivertype);

		$altname = ($drivertype === 'bind') ? 'named' : $drivertype;

		$initfile = getLinkCustomfile("/home/{$drivertype}/etc/init.d", "$altname.init");

		if (file_exists($initfile)) {
			lxfile_cp($initfile, "/etc/init.d/{$altname}");
		}
	}

	static function setBaseDnsConfig($drivertype = null)
	{
	}

	function createConfFile()
	{
		global $gbl, $sgbl, $login, $ghtml;

		$this->syncAddFile($this->main->nname);
	
		foreach ((array)$this->main->__var_addonlist as $d) {
			$this->syncAddFile($d->nname);
		}
	
	}

	function syncAddFile($domainname)
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

		self::setCreateConfFile($input);
	}

	static function setCreateConfFile($input)
	{
		$driverapp = slave_get_driver('dns');

		$altname = ($driverapp === 'bind') ? 'named' : $driverapp;

		$tplsource = getLinkCustomfile("/home/{$driverapp}/tpl", "domains.conf.tpl");
		$tpltarget = "/home/{$driverapp}/conf/master/" . $input['domainname'];

		$tpl = file_get_contents($tplsource);

		$tplparse = getParseInlinePhp($tpl, $input);

		if ($tplparse) {
			file_put_contents($tpltarget, $tplparse);
		}
	}

	function syncCreateConf()
	{
		global $gbl, $sgbl, $login, $ghtml;

		$driverapp = slave_get_driver('dns');

		$input = array();

		$tplsource = getLinkCustomfile("/home/{$driverapp}/tpl", "list.master.conf.tpl");

		$tpl = file_get_contents($tplsource);

		$tplparse = getParseInlinePhp($tpl, $input);
	}

	function createAllowTransferIps()
	{
		global $gbl, $sgbl, $login, $ghtml;

		$driverapp = slave_get_driver('dns');

		$input = array();

		$input['ip'] = $this->main->getIpfromARecord();

		$tplsource = getLinkCustomfile("/home/{$driverapp}/tpl", "list.transfered.conf.tpl");

		$tpl = file_get_contents($tplsource);

		$tplparse = getParseInlinePhp($tpl, $input);
	}

	function dbactionAdd()
	{
		$this->createConfFile();
		$this->syncCreateConf();
		$this->createAllowTransferIps();
	}

	function dbactionUpdate($subaction)
	{
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
				$this->createAllowTransferIps();
				$this->createAllowTransferIps();
				$this->syncCreateConf();
				break;
		}
	}

	function dbactionDelete()
	{
		global $gbl, $sgbl, $login, $ghtml;

		$driverapp = slave_get_driver('dns');

		$domainname = $this->main->nname;

		$dnsfile = "/home/{$driverapp}/conf/master/{$domainname}";
		lxfile_rm($dnsfile);

		foreach ((array)$this->main->__var_addonlist as $d) {
			$addondomain = $d->nname;
			$dnsfile = "/home/{$driverapp}/conf/master/{$addondomain}";
			lxfile_rm($dnsfile);
		}

		$this->syncCreateConf();
		$this->createAllowTransferIps();
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
		}
	}
}