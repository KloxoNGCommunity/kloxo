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

		lxfile_cp(getLinkCustomfile("/home/{$altname}/etc/init.d", "$altname.init"),
			"/etc/rc.d/init.d/{$altname}");
	}

	static function setBaseDnsConfig($drivertype = null)
	{
	}

	function createConfFile()
	{
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

		createRestartFile($altname);
	}

	function syncCreateConf()
	{
		global $gbl, $sgbl, $login, $ghtml;

		$driverapp = slave_get_driver('dns');

		$input = array();

		$input['domainname'] = $this->main->nname;

		$tplsource = getLinkCustomfile("/home/{$driverapp}/tpl", "list.master.conf.tpl");

		$tpl = file_get_contents($tplsource);

		// MR -- no need file_input_contents because this process inside tplsource
		$tplparse = getParseInlinePhp($tpl, $input);
	

	function getIpfromDns()
	{
	}

	function createAllowTransferIps()
	{
		global $gbl, $sgbl, $login, $ghtml;

		$driverapp = slave_get_driver('dns');

		$input = array();

		$input['ip'] = $this->getIpfromDns();

		$tplsource = getLinkCustomfile("/home/{$driverapp}/tpl", "list.transfered.conf.tpl");

		$tpl = file_get_contents($tplsource);

		// MR -- no need file_input_contents because this process inside tplsource
		$tplparse = getParseInlinePhp($tpl, $input);
	}

	function dbactionAdd()
	{
		$this->createConfFile();
		$this->syncCreateConf();
	}

	function dbactionUpdate($subaction)
	{
		$this->createConfFile();
		$this->syncCreateConf();
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
	}

	function dosyncToSystemPost()
	{
		global $sgbl;

	//	$this->createAllowTransferIps();

		$driverapp = slave_get_driver('dns');

		if ($driverapp === 'bind') {
			if ($this->main->isDeleted()) {
				createRestartFile("named");

				return;
			}

			$total = false;
			$ret = lxshell_return("rndc", "reload", $this->main->nname);

			if ($ret) {
				$total = true;
			}

			foreach ((array)$this->main->__var_addonlist as $d) {
				$ret = lxshell_return("rndc", "reload", $d->nname);

				if ($ret) {
					$total = true;
				}
			}

			if ($total) {
				$ret = lxshell_return("rndc", "reload");

				if ($ret) {
					createRestartFile("named");
				}
			}
		} elseif ($driverapp === 'djbdns') {
			createRestartFile("djbdns");
		}
	}
}