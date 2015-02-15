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
			exec("killall -9 named");
		}

		if ($driver === 'maradns') {
			$a = array('', '.deadwood', '.zoneserver');

			foreach ($a as $v) {
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
			
			if (!file_exists("/var/log/named")) {
				exec("mkdir -p /var/log/named");
			}
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

	function createConfFile()
	{
		global $sgbl;

		$input = array();

		$domains[] = $this->main->nname;

		foreach ((array)$this->main->__var_addonlist as $d) {
			$domains[] = $d->nname;
		}

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

		foreach ($dnsdrvlist as $v) {
			if (($v === 'bind') || ($v === 'yadifa')) { continue; }

			$tplsource = getLinkCustomfile("/opt/configs/{$v}/tpl", "domains.conf.tpl");
			$tpl = file_get_contents($tplsource);

			foreach ($domains as $d) {
				$input['domainname'] = $d;

				$tpltarget = "/opt/configs/{$v}/conf/master/{$d}";

				if (($v === 'pdns') || (($v === 'mydns'))) {
					getParseInlinePhp($tpl, $input);
				} else {
					$tplparse = getParseInlinePhp($tpl, $input);

					file_put_contents($tpltarget, $tplparse);
				}
			}
		}
	}

	function syncCreateConf()
	{
		$input = array();

		$ip_dns = $this->getIps();
		$ip_hostname = array(gethostbyname(php_uname('n')));
		// MR -- IP list without hostname IP
		$input['ips'] = array_diff($ip_dns, $ip_hostname);

		$input['rootpass'] = slave_get_db_pass();

		$dnsdrvlist = getAllDnsDriverList();

		$mlist = $this->getMasterList();
		$slist = $this->getSlaveList();
		$rlist = $this->getReverseList();

		foreach ($dnsdrvlist as $v) {
			$input['domains'] = $mlist;
			$tplsource = getLinkCustomfile("/opt/configs/{$v}/tpl", "list.master.conf.tpl");
			$tpl = file_get_contents($tplsource);
			getParseInlinePhp($tpl, $input);

			$input['domains'] = $slist;
			$tplsource = getLinkCustomfile("/opt/configs/{$v}/tpl", "list.slave.conf.tpl");
			$tpl = file_get_contents($tplsource);
			getParseInlinePhp($tpl, $input);

			$input['arpas'] = $rlist;
			$tplsource = getLinkCustomfile("/opt/configs/{$v}/tpl", "list.reverse.conf.tpl");
			$tpl = file_get_contents($tplsource);
			getParseInlinePhp($tpl, $input);
		}
	}

	function createAllowTransferIps()
	{
		$input = array();
	/*
		$ip_dns = $this->getIps();
		$ip_hostname = array(gethostbyname(php_uname('n')));
		// MR -- IP list without hostname IP
		$input['ips'] = array_diff($ip_dns, $ip_hostname);
	*/

		$input['ips'] = $this->getIps();

		$input['rootpass'] = slave_get_db_pass();

		$dnsdrvlist = getAllDnsDriverList();

		foreach ($dnsdrvlist as $v) {
			$tplsource = getLinkCustomfile("/opt/configs/{$v}/tpl", "list.transfered.conf.tpl");

			$tpl = file_get_contents($tplsource);

			getParseInlinePhp($tpl, $input);
		}
	}

	function getIps()
	{
		$nobase = true;

		$ret = rl_exec_get('localhost', 'localhost', 'getIpfromARecord', array($this->main->syncserver, $nobase));

		return $ret;
	}

	function getMasterList()
	{
		$ret = rl_exec_get('localhost', 'localhost', 'getDnsMasters', array($this->main->syncserver));

		return $ret;
	}

	function getSlaveList()
	{
		$ret = rl_exec_get('localhost', 'localhost', 'getDnsSlaves', array($this->main->syncserver));

		return $ret;
	}

	function getReverseList()
	{
		$ret = rl_exec_get('localhost', 'localhost', 'getDnsReverses', array($this->main->syncserver));

		return $ret;
	}

	function dbactionAdd()
	{
		$this->main->write();

		$this->createConfFile();
		$this->createAllowTransferIps();
		$this->syncCreateConf();
	}

	function dbactionDelete()
	{
		$this->main->write();

		$this->createAllowTransferIps();
		$this->syncCreateConf();
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
			case "synchronize_fix":
				$this->syncCreateConf();

				break;
			case "domain":
				$this->createConfFile();

				break;
			case "full_update":
			default:
				$this->createConfFile();
				$this->createAllowTransferIps();
				$this->syncCreateConf();

				break;
		}
	}

	function dosyncToSystemPost()
	{
		// MR -- no need action
	}
}