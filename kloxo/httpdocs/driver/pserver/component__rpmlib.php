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
		global $sgbl;

/*
		$comps = array('mysql', 'MariaDB-server', 'postgresql', 'sqlite',
			'httpd', 'lighttpd', 'nginx', 'hiawatha', 'openlitespeed', 'monkey', 'h2o',
			'trafficserver', 'varnish', 'squid',
			'php', 'perl', 'mono', 'ruby', 'nodejs',
			'bind', 'djbdns', 'pdns', 'nsd', 'mydns', 'yadifa',
			'qmail-toaster', 'pure-ftpd');

		foreach ($comps as $k => $c) {
		//	$list[]['componentname'] = $c;

			$tmp = rl_exec_get('localhost', $syncserver, 'getRpmBranchInstalled', array('$c'));

			if ($tmp) {
				$list[]['componentname'] = $tmp;
			} else {
				$list[]['componentname'] = $c;
			}
		}

		foreach($list as $l) {
			$nlist[] = $l['componentname'];
		}

		$complist = implode(" ", $nlist);

		$file = fix_nname_to_be_variable("rpm -q $complist");
		$file = "$sgbl->__path_program_root/cache/$file";

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
*/

		$mysql_list = explode(",", file_get_contents("../etc/list/set.mysql.lst"));
		$mysql_list[] = 'sqlite';
		$mysql_list[] = 'postgresql';

		$out = null;
		exec('rpm -q ' . implode(" ", $mysql_list), $out);

		foreach ($mysql_list as $k => $v) {
			$nname = "{$v}___{$syncserver}";
			$componentname = $v;
			$version = $out[$k];
			$status = (strpos($out[$k], 'is not installed') !== false) ? 'off' : 'on';

			$t[] = array('nname' => $nname, 'componentname' => $componentname, 
				'version' => "[Database] ". $version, 'status' => $status);
		}

		$web_list = explode(",", file_get_contents("../etc/list/set.web.lst"));

		$out = null;
		exec('rpm -q ' . implode(" ", $web_list), $out);

		foreach ($web_list as $k => $v) {
			$nname = "{$v}___{$syncserver}";
			$componentname = $v;
			$version = $out[$k];
			$status = (strpos($out[$k], 'is not installed') !== false) ? 'off' : 'on';

			$t[] = array('nname' => $nname, 'componentname' => $componentname, 
				'version' => "[Web] ". $version, 'status' => $status);
		}

		$webcache_list = explode(",", file_get_contents("../etc/list/set.webcache.lst"));

		$out = null;
		exec('rpm -q ' . implode(" ", $webcache_list), $out);

		foreach ($webcache_list as $k => $v) {
			$nname = "{$v}___{$syncserver}";
			$componentname = $v;
			$version = $out[$k];
			$status = (strpos($out[$k], 'is not installed') !== false) ? 'off' : 'on';

			$t[] = array('nname' => $nname, 'componentname' => $componentname, 
				'version' => "[Webcache] ". $version, 'status' => $status);
		}

		$dns_list = explode(",", file_get_contents("../etc/list/set.dns.lst"));

		$out = null;
		exec('rpm -q ' . implode(" ", $dns_list), $out);

		foreach ($dns_list as $k => $v) {
			$nname = "{$v}___{$syncserver}";
			$componentname = $v;
			$version = $out[$k];
			$status = (strpos($out[$k], 'is not installed') !== false) ? 'off' : 'on';

			$t[] = array('nname' => $nname, 'componentname' => $componentname, 
				'version' => "[DNS] ". $version, 'status' => $status);
		}

		$smtp_list = explode(",", file_get_contents("../etc/list/set.smtp.lst"));
		$spam_list = explode(",", file_get_contents("../etc/list/set.spam.lst"));
		$pop_list = explode(",", file_get_contents("../etc/list/set.pop.lst"));
		$imap_list = explode(",", file_get_contents("../etc/list/set.imap.lst"));
		$other_list = array('qmail-toaster');

		$mail_list = array_merge($smtp_list, $spam_list, $pop_list, $imap_list, $other_list);

		$out = null;
		exec('rpm -q ' . implode(" ", $mail_list), $out);

		foreach ($mail_list as $k => $v) {
			$nname = "{$v}___{$syncserver}";
			$componentname = $v;
			$version = $out[$k];
			$status = (strpos($out[$k], 'is not installed') !== false) ? 'off' : 'on';

			$t[] = array('nname' => $nname, 'componentname' => $componentname, 
				'version' => "[Mail] ". $version, 'status' => $status);
		}

		$php_list = explode(",", file_get_contents("../etc/list/set.php.lst"));
		$php_list = array_map(function($value) { return "{$value}-cli"; }, $php_list);

		$out = null;
		exec('rpm -q ' . implode(" ", $php_list), $out);

		foreach ($php_list as $k => $v) {
			$v = str_replace('-cli', '', $v);
			$nname = "{$v}___{$syncserver}";
			$componentname = $v;
			$version = $out[$k];
			$status = (strpos($out[$k], 'is not installed') !== false) ? 'off' : 'on';

			$t[] = array('nname' => $nname, 'componentname' => $componentname, 
				'version' => "[PHP Branch] " . $version, 'status' => $status);
		}

	//	$phpm_list = getMultiplePhpList();
		$phpm_list = getCleanRpmBranchListOnList('php');

		foreach ($phpm_list as $k => $v) {
			$nname = "{$v}___{$syncserver}";
			$componentname = $v;

			if (file_exists("/opt/{$v}/version")) {
				$version = "{$v}-" . file_get_contents("/opt/{$v}/version");
				$status = 'on';
			} else {
				$version = "package {$v} is not installed";
				$status = 'off';
			}

			$t[] = array('nname' => $nname, 'componentname' => $componentname, 
				'version' => "[Multiple PHP] " . $version, 'status' => $status);
		}

		return $t;
	}
}

