<?php

class dns__ extends lxDriverClass
{
	function __construct()
	{
	}

	static function getActiveDriver()
	{
		return slave_get_driver('dns');
	}

	function createConfFileTrue($drivertype, $action = null)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$this->syncAddFileTrue($drivertype, $this->main->nname, $action);
	
		foreach ((array)$this->main->__var_addonlist as $d) {
			$this->syncAddFileTrue($drivertype, $d->nname, $action);
		}
	}

	function syncAddFileTrue($drivertype, $domainname, $action = null)
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

		$tplsource = getLinkCustomfile("/opt/configs/{$drivertype}/tpl", "domains.conf.tpl");
		$tpltarget = "/opt/configs/{$drivertype}/conf/master/" . $input['domainname'];

		$tpl = file_get_contents($tplsource);

		$tplparse = getParseInlinePhp($tpl, $input);

		if ($tplparse) {
			file_put_contents($tpltarget, $tplparse);
		}
	}

	function syncCreateConfTrue($drivertype, $action = null)
	{
		global $gbl, $sgbl, $login, $ghtml;

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

		$tplsource = getLinkCustomfile("/opt/configs/{$drivertype}/tpl", "list.master.conf.tpl");

		$tpl = file_get_contents($tplsource);

		$tplparse = getParseInlinePhp($tpl, $input);
	}

	function createAllowTransferIpsTrue($drivertype)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$input = array();

		$input['ip'] = $this->getIps();

		$tplsource = getLinkCustomfile("/opt/configs/{$drivertype}/tpl", "list.transfered.conf.tpl");

		$tpl = file_get_contents($tplsource);

		$tplparse = getParseInlinePhp($tpl, $input);
	}

	function getIps()
	{
		$nobase = true;

		$iplist = rl_exec_get('localhost', 'localhost', 'getIpfromARecord', array($nobase));

		return $iplist;
	}

	function dbactionAddTrue($drivertype)
	{
		$this->createConfFileTrue($drivertype);
		$this->createAllowTransferIpsTrue($drivertype);
		$this->syncCreateConfTrue($drivertype);
	}

	function dbactionUpdateTrue($drivertype, $subaction)
	{
		global $gbl, $sgbl, $login, $ghtml;

		switch ($subaction) {
			case "allowed_transfer":
				$this->createAllowTransferIpsTrue($drivertype);

				break;
			case "synchronize":
				$this->syncCreateConfTrue($drivertype, 'update');

				break;
			case "synchronize_fix":
				$this->syncCreateConfTrue($drivertype, 'fix');

				break;
			case "domain":
				$this->createConfFileTrue($drivertype);

				break;
			case "full_update":
			default:
				$this->createConfFileTrue($drivertype);
				$this->createAllowTransferIpsTrue($drivertype);
				$this->syncCreateConfTrue($drivertype, 'update');

				break;
		}
	}

	function dbactionDeleteTrue($drivertype)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$domainname = $this->main->nname;

		$dnsfile = "/opt/configs/{$drivertype}/conf/master/{$domainname}";
		lxfile_rm($dnsfile);

		foreach ((array)$this->main->__var_addonlist as $d) {
			$addondomain = $d->nname;
			$dnsfile = "/opt/configs/{$drivertype}/conf/master/{$addondomain}";
			lxfile_rm($dnsfile);
		}

		$this->createAllowTransferIpsTrue($drivertype);
		$this->syncCreateConfTrue($drivertype);
	}

	function dosyncToSystemPostTrue($drivertype)
	{
		// MR -- no need action
	}
}