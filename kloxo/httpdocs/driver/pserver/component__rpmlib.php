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

		$files = glob("../etc/list/set.*.lst", GLOB_MARK);

		foreach ($files as $fk => $fv) {
			$b = basename($fv);

			if (file_exists("../etc/list/custom.{$b}")) {
				$fv = "../etc/list/custom.{$b}";
			}

			$type = preg_replace("(custom\.|set\.|\.lst)", "", $b);

			// MR -- no detect for httpd because include in web
			if ($type === 'httpd') { continue; }

			$list = explode(",", trim(file_get_contents($fv), "\n"));

			if ($type === 'php') {
				$type = 'php-branch';
				$list = array_map(function($value) { return "{$value}-cli"; }, $list);
			} else {
				$list = array_values(array_unique($list));
			}

			$out = null;
			exec('rpm -q ' . implode(" ", $list), $out);

			foreach ($list as $k => $v) {
				$nname = "{$v}___{$syncserver}";

				$componentname = $v;
				$version = $out[$k];
				$status = (strpos($out[$k], 'is not installed') !== false) ? 'off' : 'on';

				$t[] = array('nname' => $nname, 'componentname' => $componentname, 
					'version' => $version, 'status' => $status,
					'type' => $type);
			}
		}

		$list = getCleanRpmBranchListOnList('php');

		foreach ($list as $k => $v) {
			$nname = "{$v}___{$syncserver}";
			$type = "multiple-php";
			$componentname = $v;

			if (file_exists("/opt/{$v}/version")) {
				$version = "{$v}-" . file_get_contents("/opt/{$v}/version");
				$status = 'on';
			} else {
				$version = "package {$v} is not installed";
				$status = 'off';
			}

			$t[] = array('nname' => $nname, 'componentname' => $componentname, 
				'version' => $version, 'status' => $status,
				'type' => $type);
		}

		return $t;
	}
}
