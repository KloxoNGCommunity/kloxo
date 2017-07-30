<?php

class LxDnsClass extends Lxaclass
{
}

class dns_record_a extends LxDnsClass
{
	static $__desc = array("", "", "dns_record");
	static $__desc_nname = array("n", "", "dns_record");
	static $__desc_param = array("n", "", "value");
	static $__desc_hostname = array("n", "", "hostname", "a=updateform&sa=edit");
	static $__desc_flag = array("n", "", "flag");
	static $__desc_tag = array("n", "", "tag");
	static $__desc_ttype = array("", "", "type");
	static $__desc_ttype_v_mx = array("", "", "MX");
	static $__desc_ttype_v_ns = array("", "", "NS");
	static $__desc_ttype_v_a = array("", "", "A");
	static $__desc_ttype_v_aaaa = array("", "", "AAAA");
	static $__desc_ttype_v_txt = array("", "", "TXT");
	static $__desc_ttype_v_cname = array("", "", "CNAME");
	static $__desc_ttype_v_fcname = array("", "", "FCNAME");
	static $__desc_priority = array("", "", "priority");
	static $__desc_ttype_v_caa = array("", "", "CAA");
	static $__desc_ttype_v_srv = array("", "", "SRV");
	static $__desc_weight = array("n", "", "weight");
	static $__desc_port = array("n", "", "port");

	function isSelect()
	{
		if ($this->nname === 'a___base__') {
		//	return false;
		}

		if ($this->nname === 'a_mail') {
			return true;
		}

		return true;
	}

	function updateform($subaction, $param)
	{
		$vlist['hostname'] = array('M', null);

		if ($this->ttype === 'txt') {
			$vlist['param'] = array("t", "");
		} else {
			if (($this->ttype === 'fcname') || ($this->ttype === 'ns') || ($this->ttype === 'mx')) {
				$vlist['param'] = array('m', array('posttext' => "."));
			} else {
				$vlist['param'] = null;
			}
		}

		return $vlist;
	}

	function isAction($var)
	{
	/*
		if ($this->ttype === 'ns') {
			return false;
		}
	*/
		return true;
	}

	static function createListNlist($parent, $view)
	{
	//	$nlist['nname'] = '10%';
		$nlist['hostname'] = '20%';
		$nlist['ttype'] = '5%';
		$nlist['priority'] = '5%';
		$nlist['param'] = '70%';

		return $nlist;
	}

	static function perPage()
	{
		return 6000;
	}

	function display($var)
	{
		if (!isset($this->$var)) {
			if ($var == 'hostname') {
				$this->$var = '__base__';
			} else {
				return '-';
			}
		}

		if ($var === 'ttype') {
			return strtoupper($this->$var);
		}

		if ($var === 'param') {
			if ($this->ttype === 'txt') {
				if (strlen($this->$var) > 75) {
					return substr($this->$var, 0, 75) . "...";
				}
			}

			if ($this->ttype === 'caa') {
				return "{$this->flag} {$this->tag} \"{$this->param}\"";
			}

			if ($this->ttype === 'srv') {
				return "{$this->weight} {$this->port} \"{$this->param}\"";
			}

		/*
		//	if (strpos($this->getParentO()->nname, '.dnst') !== false) {
				// MR -- for template
			//	$this->$var = str_replace($this->getParentO()->nname, "__base__", $this->$var);
		//	} else {
				// MR -- for dns setting
				if ($this->ttype !== 'cn') {
					$this->$var = str_replace("__base__", $this->getParentO()->nname, $this->$var);
				} else {
					if (($this->$var === '__base__') || ($this->$var === $this->getParentO()->nname)) {
						$this->$var = $this->getParentO()->nname . '.';
					} else {
					//	$this->$var .= '.' . $this->getParentO()->nname . '.';
					}
				}

				if (($this->ttype === 'fcname') || ($this->ttype === 'ns') || ($this->ttype === 'mx')) {
					$this->$var .= '.';
				}
		//	}
		*/
		}

		// MR -- fix appear in 'old' data
		if ($var === 'hostname') {
			if (($this->hostname === $this->param) || ($this->hostname === '')) {
				$this->$var = '__base__';
			}

			$this->$var = str_replace($this->getParentO()->nname, "__base__", $this->$var);
			$this->$var = str_replace(".__base__", "", $this->$var);

			if ($this->$var !== '__base__') {
				// MR -- TODO: change domain.dnst to __base__; unfinish
			//	$this->$var = $this->$var . '.__base__';
			}
		}

		return $this->$var;
	}

	static function add($parent, $class, $param)
	{
		global $login;

		if ($param['ttype'] === 'mx') {
			// Validates domain
			validate_domain_name($param['param']);

			$param['nname'] = "{$param['ttype']}_{$param['priority']}";
			$param['hostname'] = $parent->nname;
		} elseif ($param['ttype'] === 'ns') {
			// MR -- make possible to 'delegate' subdomain to other server!

			// MR -- need remove __base__
			$x = str_replace('.__base__', '', $param['hostname']);

			validate_hostname_name($x);

			$a_record_match = false;

			foreach($parent->dns_record_a as $d) {
				if (($d->ttype === 'a') || ($d->ttype === 'aaa')) {
					if ($d->hostname === $x) {
						$a_record_match = true;
						break;
					}
				}
			}

			if (!$a_record_match) {
				throw new lxException($login->getThrow('need_a_or_aaa_record'), '', $x);
			}

			// Validates domain
			validate_domain_name($param['param']);

			$param['nname'] = "{$param['ttype']}_{$param['param']}";
		} elseif ($param['ttype'] === 'a' || $param['ttype'] === 'aaaa') {
			// Validates subdomain
			validate_hostname_name($param['hostname']);
			
			// MR -- make IPv6 always full format
			if (strpos($param['param'], ':') !== false) {
				$param['param'] = ipv6_expand($param['param']);
			}

			// Validates both ipv4 and ipv6
			validate_ipaddress($param['param']);

			// MR -- back to use old model (importance for round-robin 'A record'
			$param['nname'] = "{$param['ttype']}_{$param['hostname']}_{$param['param']}";
		//	$param['nname'] = "{$param['ttype']}_{$param['hostname']}";
		} elseif ($param['ttype'] === 'cname') {
			// Validates hostname subdomain
			validate_hostname_name($param['hostname']);
			validate_hostname_name($param['param']);

			$param['nname'] = "{$param['ttype']}_{$param['hostname']}";
		} elseif ($param['ttype'] === 'fcname') {
			// Validates hostname subdomain
			validate_hostname_name($param['hostname']);

			// Validates value domain
			validate_domain_name($param['param']);

			$param['nname'] = "{$param['ttype']}_{$param['hostname']}";
		} elseif ($param['ttype'] === 'txt') {
			// Validates hostname subdomain

			$val = $param['hostname'];

			// MR -- handle hostname like _domainkey and _dmarc
			if ((strpos($val, '_', 0) !== false)) {
				$val = str_replace('_', '', $val);
			}

			validate_hostname_name($val);

			$param['nname'] = "{$param['ttype']}_{$val}";
		} elseif ($param['ttype'] === 'caa') {
			// Validates hostname subdomain

			$val =  str_replace('.__base__', '', $param['hostname']);

			validate_hostname_name($val);

			validate_domain_name($param['param']);

			$param['nname'] = "{$param['ttype']}_{$param['param']}";
		} elseif ($param['ttype'] === 'srv') {
			$val = $param['hostname'];

			// MR -- handle hostname like _domainkey and _dmarc
			if ((strpos($val, '_', 0) !== false)) {
				$val = str_replace('_', '', $val);
			}

			validate_hostname_name($val);

			$param['nname'] = "{$param['ttype']}_{$val}";
		} else {
			$param['nname'] = "{$param['ttype']}_{$param['hostname']}";
		}

		return $param;
	}

	static function addform($parent, $class, $typetd = null)
	{
		if ($typetd['val'] === 'ns') {
			// MR -- add hostname entry to make possible to 'delegate' to other server!
			$vlist['hostname'] = array('m', array('value'=> '__base__'));
			$vlist['param'] = array('m', array('posttext' => "."));
		} elseif ($typetd['val'] === 'mx') {
			$vlist['priority'] = array('s', array('5', '10', '20', '30', '40', '50', '60', '70', '80', '90', '100'));
			$vlist['param'] = array('m', array('posttext' => "."));
		} elseif ($typetd['val'] === 'cname') {
			$vlist['hostname'] = array('m', array('posttext' => ".$parent->nname."));
			$vlist['param'] = array('m', array('posttext' => ".$parent->nname."));
		} elseif ($typetd['val'] === 'fcname') {
			$vlist['hostname'] = array('m', array('posttext' => ".$parent->nname."));
			$vlist['param'] = array('m', array('posttext' => "."));
		} elseif ($typetd['val'] === 'txt') {
			$vlist['hostname'] = array('m', array('posttext' => ".$parent->nname."));
			$vlist['param'] = array('t', "");
		} elseif ($typetd['val'] === 'caa') {
			$vlist['hostname'] = array('m', array('value'=> '__base__'));
			$vlist['flag'] = array('s', array('0', '1', '128'));
			$vlist['tag'] = array('s', array('issue', 'issuewild', 'iodef'));
			$vlist['param'] = array('m', array('value'=> 'letsencrypt.org'));
		} elseif ($typetd['val'] === 'srv') {
			$vlist['hostname'] = array('m', array('posttext' => ".$parent->nname."));
			$vlist['priority'] = array('s', array('0', '5', '10', '20', '30', '40', '50', '60', '70', '80', '90', '100'));
			$vlist['weight'] = array('s', array('0', '5', '10', '20', '30', '40', '50', '60', '70', '80', '90', '100'));
			$vlist['port'] = null;
			$vlist['param'] = null;
		} else {
		//	$vlist['hostname'] = array('m', array('posttext' => ".$parent->nname."));
			$vlist['hostname'] = null;
			$vlist['param'] = null;
		}

		$ret['variable'] = $vlist;
		$ret['action'] = 'Add';

		return $ret;
	}
}

class Mx_rec_a extends LxDnsclass
{
}

class Ns_rec_a extends Lxdnsclass
{
}

class Txt_rec_a extends Lxdnsclass
{
}

class A_rec_a extends Lxdnsclass
{
}

class Cn_rec_a extends Lxdnsclass
{
}

abstract class Dnsbase extends Lxdb
{
	// Mysql
	static $__desc_ttl = array("", "", "ttl");
	static $__desc_syncserver = array("sd", "", "primary_dns");
	static $__desc_ns_rec_a = array("", "", "ns_record");
	static $__desc_a_rec_a = array("", "", "a_record");
	static $__desc_mx_rec_a = array("", "", "mx_record");
	static $__desc_cn_rec_a = array("", "", "cn_record");
	static $__desc_fcname_rec_a = array("", "", "fcname_record");
	static $__desc_zone_type = array("", "", "type_of_dns_zone_file");
	static $__desc_nameserver_f = array("n", "", "primary_DNS");
	static $__desc_newdnstemplate_f = array("n", "", "new_dns_template");
	static $__desc_secnameserver_f = array("", "", "secondary_DNS");
	static $__desc_soanameserver = array("", "", "SOA_nameserver");
	static $__desc_hostmaster = array("", "", "hostmaster_email");
	static $__acdesc_update_parameter = array("", "", "general_settings");
	static $__acdesc_update_switchdnsserver = array("", "", "switch_server");
	static $__acdesc_update_rebuild = array("", "", "rebuild");

	function createDefaultTemplate($webipaddress, $mmailipaddress = "0.0.0.0.0", $nameserver = "defaultnameserver", $secnamserver = null)
	{
		global $login;

	//	$this->ttl = "86000";
		// MR -- based on https://www.ietf.org/rfc/rfc1912.txt
		$this->ttl = "1209600";

		validate_domain_name($nameserver);

		$this->addRec('ns', $nameserver, $nameserver);

		if ($secnamserver) {
			validate_domain_name($nameserver);
		
			$this->addRec('ns', $secnamserver, $secnamserver);
		}

		// Extra dot added at the end of a_rec...
		$cpip = getOneIPForServer("localhost");

		if (!$cpip) {
			$cpip = $webipaddress;
		}

		$this->addRec("a", "cp", $cpip);
		$this->addRec("a", "__base__", $webipaddress);
	//	$this->addRec("a", "ns", $webipaddress);
		$this->addRec("a", "ns1", $webipaddress);
		$this->addRec("a", "ns2", $webipaddress);
		$this->addRec("a", "mail", $mmailipaddress);
		$this->addRec("cn", "www", "__base__");
	//	$this->addRec("cn", "ftp", "__base__");
		$this->addRec("a", "ftp", $webipaddress);
		$this->addRec("a", "stats", $webipaddress);
		$this->addRec("cn", "webmail", "mail");
		$this->addRec("cn", "lists", "mail");
		$this->addRec("fcname", "smtp", "mail.$this->nname");
		$this->addRec("fcname", "pop", "mail.$this->nname");
		$this->addRec("fcname", "imap", "mail.$this->nname");
		$this->addRec("mx", "10", "mail.$this->nname");

		return;
	}

	function addRec($ttype, $name, $param)
	{
		$rname = "{$ttype}_$name";
		$__temp = new dns_record_a(null, null, $rname);

		if ($ttype === 'mx') {
			$__temp->hostname = $this->nname;
			$__temp->priority = $name;
			$__temp->param = $param;
		} elseif ($ttype === 'ns') {
			// MR -- add 'hostname' and set '__base__' as default
			$__temp->hostname = "__base__";
			$__temp->param = $param;
		} else {
			$__temp->hostname = $name;
			$__temp->param = $param;
		}

		$__temp->ttype = $ttype;

		if (!isset($this->dns_record_a)) {
			$this->dns_record_a = array();
		}

		$this->dns_record_a[$rname] = $__temp;
		$this->setUpdateSubaction('subdomain');
	}

	function addDomainKey($key)
	{
		$this->addRec("txt", "_domainkey", "t=y; o=-; r=admin@{$this->nname}");
	//	$this->addRec("txt", "private._domainkey", "k=rsa; p=$key");
		$this->addRec("txt", "private._domainkey", "v=DKIM1; g=*; k=rsa; p=$key");
	}

	function RemoveDomainKey()
	{
		foreach ($this->dns_record_a as $k => $v) {
			if ($v->ttype === 'txt' && ($v->hostname === "_domainkey" || $v->hostname === 'private._domainkey')) {
				dprint("removing domainkey for $this->nname\n");

				unset($this->dns_record_a[$k]);
			}
		}
	}

	function getIpForBaseDomain()
	{
		foreach ($this->dns_record_a as $d) {
			if ($d->ttype === 'a' && $d->hostname === '__base__') {
				return $d->param;
			}
		}

		return '0.0.0.0';
	}

	function copyObject($dns)
	{
	//	$this->ipaddress = $dns->ipaddress;
		$this->ttl = $dns->ttl;

		if ($dns->isClass('dns')) {
			$this->soanameserver = $dns->soanameserver;
		} else {
			$this->soanameserver = str_replace($dns->nname, $this->nname, $dns->soanameserver);
		}

		$this->zone_type = $dns->zone_type;
		$name = $dns->nname;

		foreach ($dns->dns_record_a as $k => $o) {
			if ($dns->isClass('dns') && $o->ttype === 'ns') {
				// MR -- add 'hostname'
				$hostname = $o->hostname;
				$param = $o->param;
				$nname = $o->nname;
			} else {
				$hostname = str_replace($dns->nname, $this->nname, $o->hostname);
				$param = str_replace($dns->nname, $this->nname, $o->param);
				$nname = str_replace($dns->nname, $this->nname, $o->nname);
			}

			$this->dns_record_a[$nname] = new dns_record_a(null, null, $nname);
			$this->dns_record_a[$nname]->hostname = $hostname;
			$this->dns_record_a[$nname]->ttype = $o->ttype;

			if (isset($o->priority)) {
				$this->dns_record_a[$nname]->priority = $o->priority;
			}

			$this->dns_record_a[$nname]->param = $param;
		}
	}

	function copyObjectWithSave($dnstemplate)
	{
		$saved = null;

		foreach ($this->dns_record_a as $k => $v) {
			if ($v->ttype === 'txt') {
				$saved[$k] = $v;
			}
		}

		$this->dns_record_a = null;
		$this->copyObject($dnstemplate);

		foreach ($saved as $k => $v) {
			if (!isset($this->dns_record_a[$k])) {
				$this->dns_record_a[$k] = $v;
			}
		}
	}

	function updateRebuild($param)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$dnstemplatename = $param['newdnstemplate_f'];
		$dnstemplate = new Dnstemplate($this->__masterserver, $this->__readserver, $dnstemplatename);

		// If template get the ip from the template.
		$dnstemplate->get();
		$this->copyObjectWithSave($dnstemplate);
		$gbl->__ajax_refresh = true;
		$this->rootpassword_changed = 'on';

		return $param;
	}

	function postAdd()
	{
		$this->createDefaultTemplate($this->webipaddress, $this->mmailipaddress, $this->nameserver_f, $this->secnameserver_f);
	}

	function createShowClist($subaction)
	{
		$clist["dns_record_a"] = null;

		return $clist;
	}

	function isRightParent()
	{
		return ($this->getParentO()->getClName() === $this->parent_clname);
	}

	function createShowPropertyList(&$alist)
	{
		global $gbl, $sgbl, $login, $ghtml;

		if ($this->isRightParent()) {
			$alist['property'][] = "a=show";

			if (!cse($this->get__table(), "template") && $sgbl->isKloxo()) {
				$alist['property'][] = "a=updateform&sa=rebuild";
			}

			$alist['property'][] = 'a=addform&c=dns_record_a&dta[var]=ttype&dta[val]=ns';
			$alist['property'][] = 'a=addform&c=dns_record_a&dta[var]=ttype&dta[val]=a';
			$alist['property'][] = 'a=addform&c=dns_record_a&dta[var]=ttype&dta[val]=aaaa';
			$alist['property'][] = 'a=addform&c=dns_record_a&dta[var]=ttype&dta[val]=cname';
			$alist['property'][] = 'a=addform&c=dns_record_a&dta[var]=ttype&dta[val]=fcname';
			$alist['property'][] = 'a=addform&c=dns_record_a&dta[var]=ttype&dta[val]=mx';
			$alist['property'][] = 'a=addform&c=dns_record_a&dta[var]=ttype&dta[val]=txt';
			$alist['property'][] = 'a=addform&c=dns_record_a&dta[var]=ttype&dta[val]=caa';
			$alist['property'][] = 'a=addform&c=dns_record_a&dta[var]=ttype&dta[val]=srv';
			$alist['property'][] = 'a=updateform&sa=parameter';

		//	$alist[] = 'a=updateform&sa=parameter';
		}
	}

	function fixParentClName()
	{
		foreach ($this->dns_record_a as $d) {
			if (isset($d->parent_clname)) {
				return;
			}

			$d->parent_clname = $this->getClName();
		}

		$this->setUpdateSubaction();
		$this->write();
	}

	function createShowAlist(&$alist, $subaction = null)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$this->fixParentClName();

		return $alist;
	}

	static function getIpaddressList($parent)
	{
		$db = new Sqlite($parent->__masterserver, 'ipaddress');
		$res = $db->getTable(array('ipaddr'));
		$res = get_namelist_from_arraylist($res, 'ipaddr');

		return $res;
	}

	function updateform($subaction, $param)
	{
		global $gbl, $sgbl, $login, $ghtml;

		switch ($subaction) {

			// ONly from dnstemplate
			case "ipaddress":
				$res = self::getIpaddressList($this);
				$vlist['ipaddress'] = array('s', $res);
				return $vlist;

			case "parameter":
				foreach ($this->dns_record_a as $d) {
					if ($d->ttype === 'ns') {
						$nslist[] = $d->param;
					}
				}

				$vlist['ttl'] = null;
				$vlist['soanameserver'] = array('s', $nslist);
				$this->setDefaultValue('hostmaster', "admin@{$this->nname}");
				$vlist['hostmaster'] = null;
				return $vlist;

			case "switchdnsserver":
				$vlist['syncserver'] = array('s', $login->getServerList('syncserver'));
				return $vlist;


			case "rebuild":
				$vlist['newdnstemplate_f'] = array('s', domainbase::getDnsTemplateList($login));
				$vlist['__v_updateall_button'] = array();
				return $vlist;

		}

		return parent::updateform($subaction, $param);
	}

	function updateSwitchDnsServer($param)
	{
		global $gbl, $sgbl, $login, $ghtml;

		// Not much checking is needed now. You just add the files. Don't delete it from the old place.
		// After all it is just one single dns file. We will come up with a better logic later.

		$this->syncserver = $param['syncserver'];

		$domain = $this->getParentO();
		$domain->dnspserver = $this->syncserver;
		$domain->setUpdateSubaction();
		$domain->write();

		$this->dbaction = 'syncadd';
		$this->was();

		$ghtml->print_redirect_back_success('dns_switched_successfuly', null);

		exit;
	}
}
