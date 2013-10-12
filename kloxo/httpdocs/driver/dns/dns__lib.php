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

		$this->syncAddFileTrue($this->main->nname, $action);
	
		foreach ((array)$this->main->__var_addonlist as $d) {
			$this->syncAddFileTrue($d->nname, $action);
		}
	}

	function syncAddFileTrue($domainname, $action = null)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$drivertype = self::getActiveDriver();

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

		$tplsource = getLinkCustomfile("/home/{$drivertype}/tpl", "domains.conf.tpl");
		$tpltarget = "/home/{$drivertype}/conf/master/" . $input['domainname'];

		$tpl = file_get_contents($tplsource);

		$tplparse = getParseInlinePhp($tpl, $input);

		if ($tplparse) {
			file_put_contents($tpltarget, $tplparse);
		}
	}

	function syncCreateConfTrue($action = null)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$drivertype = self::getActiveDriver();

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

		$tplsource = getLinkCustomfile("/home/{$drivertype}/tpl", "list.master.conf.tpl");

		$tpl = file_get_contents($tplsource);

		$tplparse = getParseInlinePhp($tpl, $input);
	}

	function createAllowTransferIpsTrue()
	{
		global $gbl, $sgbl, $login, $ghtml;

		$drivertype = self::getActiveDriver();

		$input = array();

		$input['ip'] = $this->getIpsTrue();

		$tplsource = getLinkCustomfile("/home/{$drivertype}/tpl", "list.transfered.conf.tpl");

		$tpl = file_get_contents($tplsource);

		$tplparse = getParseInlinePhp($tpl, $input);
	}

	function getIpsTrue()
	{
		$nobase = true;

		$iplist = rl_exec_get('localhost', 'localhost', 'getIpfromARecord', array($nobase));

		return $iplist;
	}

	function dbactionAddTrue()
	{
		$this->createConfFileTrue();
		$this->createAllowTransferIpsTrue();
		$this->syncCreateConfTrue();
	}

	function dbactionUpdateTrue($subaction)
	{
		global $gbl, $sgbl, $login, $ghtml;

		switch ($subaction) {
			case "allowed_transfer":
				$this->createAllowTransferIpsTrue();

				break;
			case "synchronize":
				$this->syncCreateConfTrue('update');

				break;
			case "synchronize_fix":
				$this->syncCreateConfTrue('fix');

				break;
			case "domain":
				$this->createConfFileTrue();

				break;
			case "full_update":
			default:
				$this->createConfFileTrue();
				$this->createAllowTransferIpsTrue();
				$this->syncCreateConfTrue('update');

				break;
		}
	}

	function dbactionDeleteTrue()
	{
		global $gbl, $sgbl, $login, $ghtml;

		$drivertype = self::getActiveDriver();

		$domainname = $this->main->nname;

		$dnsfile = "/home/{$drivertype}/conf/master/{$domainname}";
		lxfile_rm($dnsfile);

		foreach ((array)$this->main->__var_addonlist as $d) {
			$addondomain = $d->nname;
			$dnsfile = "/home/{$drivertype}/conf/master/{$addondomain}";
			lxfile_rm($dnsfile);
		}

		$this->createAllowTransferIpsTrue();
		$this->syncCreateConfTrue();
	}

	function dosyncToSystemPostTrue()
	{
		// MR -- no need action
	}
}