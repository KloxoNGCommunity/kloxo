<?php

class dns__ extends lxDriverClass
{
	function __construct()
	{
	}

	static function unInstallMeTrue($driver = null)
	{
		if ($driver === 'bind') {
			$driveralias = 'named';
		} else {
			$driveralias = $driver;
		}

		setRpmRemoved($driver);

		if ($driver === 'bind') {
			setRpmInstalled("bind-utils");
		}

		if ($driver === 'maradns') {
			$a = array('', '.deadwood', '.zoneserver');

			foreach ($a as $k => $v) {
				exec("service {$driveralias}{$v} stop");
				lunlink("/etc/init.d/{$driveralias}{$v}");
			}
		} else {
			lunlink("/etc/init.d/{$driveralias}");
		}
	}

	static function installMeTrue($driver = null)
	{
		if ($driver === 'bind') {
			$driveralias = 'named';
		} else {
			$driveralias = $driver;
		}

		setRpmInstalled($driver);

		if ($driver === 'bind') {
		//	setRpmInstalled("{$driver}-utils");
			setRpmRemoved("{$driver}-chroot");
			setRpmInstalled("{$driver}-libs");
		} elseif ($driver === 'pdns') {
			setRpmInstalled("{$driver}-backend-mysql");
			setRpmInstalled("{$driver}-tools");
			setRpmInstalled("{$driver}-geo");
		} elseif ($driver === 'mydns') {
			setRpmInstalled("{$driver}-mysql");
		}


		$initfile = getLinkCustomfile("/opt/configs/{$driver}/etc/init.d", "{$driveralias}.init");

		if (file_exists($initfile)) {
			lxfile_cp($initfile, "/etc/init.d/{$driveralias}");
			chmod("/etc/init.d/{$driveralias}", '0755');
		}

		setCopyDnsConfFiles($driver);

		if ($driver === 'djbdns') {
			lxshell_return("/etc/init.d/djbdns", "setup");
		} elseif ($driver === 'nsd') {
			$path = "/opt/configs/nsd/conf/defaults";

			if (!file_exists("{$path}/{$driver}.master.conf")) {
				touch("{$path}/{$driver}.master.conf");
			}

			if (!file_exists("{$path}/nsd.slave.conf")) {
				touch("{$path}/nsd.slave.conf");
			}
		}

		lxshell_return("chkconfig", $driveralias, "on");

		// MR -- disable here because execute in switchProgramPost()
	//	createRestartFile($driveralias);
	//	createRestartFile("restart-dns");
	}

	static function getActiveDriver()
	{
		return slave_get_driver('dns');
	}

	function createConfFileTrue($drivertype)
	{
		$this->syncAddFileTrue($drivertype, $this->main->nname);

		foreach ((array)$this->main->__var_addonlist as $d) {
			$this->syncAddFileTrue($drivertype, $d->nname);
		}
	}

	function syncAddFileTrue($drivertype, $domainname)
	{
		global $sgbl;

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

		$input['rootpass'] = slave_get_db_pass();

		$dnsdrvlist = getAllDnsDriverList();

		foreach ($dnsdrvlist as $k => $v) {
			$tplsource = getLinkCustomfile("/opt/configs/{$v}/tpl", "domains.conf.tpl");
			$tpltarget = "/opt/configs/{$v}/conf/master/" . $input['domainname'];

			$tpl = file_get_contents($tplsource);

			if ($v !== 'pdns') {
				$tplparse = getParseInlinePhp($tpl, $input);

				file_put_contents($tpltarget, $tplparse);
			} else {
				getParseInlinePhp($tpl, $input);
			}
		}
	}

	function syncCreateConfTrue($drivertype)
	{
		$input = array();

		$ip_dns = $this->getIps();
		$ip_hostname = array(gethostbyname(php_uname('n')));
		// MR -- IP list without hostname IP
		$input['ips'] = array_diff($ip_dns, $ip_hostname);

		$domains = array();

		$domains[] = $this->main->nname;

		foreach ((array)$this->main->__var_addonlist as $d) {
			$domains[] = $d->nname;
		}

		$input['rootpass'] = slave_get_db_pass();

		$dnsdrvlist = getAllDnsDriverList();

		foreach ($domains as $d) {
			$input['domain'] = $d;

			foreach ($dnsdrvlist as $k => $v) {
				$input['domains'] = $this->getMasterList();
				$tplsource = getLinkCustomfile("/opt/configs/{$v}/tpl", "list.master.conf.tpl");
				$tpl = file_get_contents($tplsource);
				getParseInlinePhp($tpl, $input);

				$input['domains'] = $this->getSlaveList();
				$tplsource = getLinkCustomfile("/opt/configs/{$v}/tpl", "list.slave.conf.tpl");
				$tpl = file_get_contents($tplsource);
				getParseInlinePhp($tpl, $input);
			}
		}
	}

	function createAllowTransferIpsTrue($drivertype)
	{
		$input = array();

		$ip_dns = $this->getIps();
		$ip_hostname = array(gethostbyname(php_uname('n')));
		// MR -- IP list without hostname IP
		$input['ips'] = array_diff($ip_dns, $ip_hostname);

		$input['rootpass'] = slave_get_db_pass();

		$dnsdrvlist = getAllDnsDriverList();

		foreach ($dnsdrvlist as $k => $v) {
			$tplsource = getLinkCustomfile("/opt/configs/{$v}/tpl", "list.transfered.conf.tpl");

			$tpl = file_get_contents($tplsource);

			getParseInlinePhp($tpl, $input);
		}
	}

	function getIps()
	{
		$nobase = true;

		$ret = rl_exec_get('localhost', 'localhost', 'getIpfromARecord', array($nobase));

		return $ret;
	}

	function getMasterList()
	{
		$ret = rl_exec_get('localhost', 'localhost', 'getDnsMasters');

		return $ret;
	}

	function getSlaveList()
	{
		$ret = rl_exec_get('localhost', 'localhost', 'getDnsSlaves');

		return $ret;
	}

	function dbactionAddTrue($drivertype)
	{
		$this->createConfFileTrue($drivertype);
		$this->createAllowTransferIpsTrue($drivertype);
		$this->syncCreateConfTrue($drivertype);
	}

	function dbactionUpdateTrue($drivertype, $subaction)
	{
		switch ($subaction) {
			case "allowed_transfer":
				$this->createAllowTransferIpsTrue($drivertype);

				break;
			case "synchronize":
				$this->syncCreateConfTrue($drivertype);

				break;
			case "synchronize_fix":
				$this->syncCreateConfTrue($drivertype);

				break;
			case "domain":
				$this->createConfFileTrue($drivertype);

				break;
			case "full_update":
			default:
				$this->createConfFileTrue($drivertype);
				$this->createAllowTransferIpsTrue($drivertype);
				$this->syncCreateConfTrue($drivertype);

				break;
		}
	}

	function dbactionDeleteTrue($drivertype)
	{
		$dnsdrvlist = getAllDnsDriverList();

		foreach ($dnsdrvlist as $k => $v) {
			$this->createAllowTransferIpsTrue($v);
			$this->syncCreateConfTrue($v);
		}
	}

	function dosyncToSystemPostTrue($drivertype)
	{
		// MR -- no need action
	}
}