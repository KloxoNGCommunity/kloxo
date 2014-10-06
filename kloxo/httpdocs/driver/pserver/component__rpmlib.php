<?php

class Component__rpm extends lxDriverClass
{
	static function getDetailedInfo($name)
	{
		$ret = lxshell_output("rpm", "-qi", $name);

		return $ret;
	}

	static function getVersion($list, $name)
	{
		foreach($list as $v) {
			if (csb($v, $name) || csa($v, " $name ")) {
				$ret[] = $v;
			}
		}

		return implode(", ", $ret);
	}

	static function getListVersion($syncserver, $list)
	{
	/*
		$comps = array('mysql', 'postgresql', 
			'httpd', 'lighttpd', 'nginx', 'hiawatha', 'openlitespeed',
			'trafficserver', 'varnish', 'squid',
			'php', 'perl', 'mono', 'ruby', 
			'bind', 'djbdns', 'maradns', 'pdns', 'nsd',
			'qmail-toaster', 'pure-ftpd');
	*/

		$comps = array('mysql', 'postgresql', 
			'httpd', 'lighttpd', 'nginx', 'hiawatha', 'openlitespeed',
			'trafficserver', 'varnish', 'squid',
			'php', 'perl', 'mono', 'ruby', 
			'bind', 'djbdns', 'pdns', 'nsd',
			'qmail-toaster', 'pure-ftpd');

		foreach ($comps as $k => $c) {
		//	$list[]['componentname'] = $c;

			if (getRpmBranchInstalled($c)) {
				$list[]['componentname'] = getRpmBranchInstalled($c);
			} else {
				$list[]['componentname'] = $c;
			}
		}

		foreach($list as $l) {
			$nlist[] = $l['componentname'];
		}

		$complist = implode(" ", $nlist);

		$file = fix_nname_to_be_variable("rpm -q $complist");
		$file = "__path_program_root/cache/$file";

		$cmdlist = lx_array_merge(array(array("rpm", "-q"), $nlist));
		$val = get_with_cache($file, $cmdlist);

		$res = explode("\n", $val);

		$ret = null;

		foreach($list as $k => $l) {
			$name = $list[$k]['componentname'];
			$sing['nname'] = $name . "___" . $syncserver;
			$sing['componentname'] = $name;

			$sing['version'] = self::getVersion($res, $name);
			$status = strstr($sing['version'], "not installed");
			$sing['status'] = $status? 'off': 'on';

			$ret[] = $sing;
		}

		return $ret;
	}
}


