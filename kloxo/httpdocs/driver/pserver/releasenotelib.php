<?php

class ReleaseNote extends Lxlclass
{
	static $__desc = array("S", "", "release_note");

	// Mysql
	static $__desc_nname = array("n", "", "note");
	static $__desc_version = array("n", "", "version");
	static $__desc_description = array("n", "", "description");
	static $__desc_over_r = array("e", "", "Past");
	static $__desc_over_r_v_dull = array("", "", "Old_version");
	static $__desc_over_r_v_on = array("", "", "Newer_Version");
	static $__desc_ttype = array("", "", "type");
	static $__desc_ttype_v_critical = array("s", "", "critical");
	static $__desc_ttype_v_feature = array("s", "", "enhancement");

	static function createListAlist($parent, $class)
	{
		$alist[] = "a=list&c=$class";

		return $alist;
	}

	static function createListNlist($parent, $view)
	{
	//	$nlist['over_r'] = '5%';
		$nlist['version'] = '10%';
	//	$nlist['ttype'] = '5%';
		$nlist['description'] = '90%';
		return $nlist;
	}

	static function defaultSort() { return "nname"; }

	static function defaultSortDir() { return "desc"; }

	static function perPage() { return 500; }

	function isSelect()
	{
		return false;
	}

	static function createListBlist($parent, $class)
	{
		return null;
	}

	static function parseReleaseNote()
	{
		global $gbl, $sgbl, $login, $ghtml;

		exec("sh /script/version --vertype=full", $out1);

		$ver = $out1[0];

		exec("rpm -q --changelog kloxong | less", $out2);

		$result = array();

		$x = implode("\n", $out2);

		$y = explode("* ", $x);

		$ol = strlen(sizeof($y));

		$m = str_repeat('0', $ol);

		$c = 0;

		foreach ($y as $k => $v) {
			if ($v === '') { continue; }

			$c++;

			$e = abs($c - count($y));
			$b = strlen($e);
			$d = substr_replace($m, $e, -$b);

		//	$newvar['version'] = $ver;
			$newvar['version'] = $d;

			$newvar['over_r'] = 'on';

			$newvar['ttype'] = $d;
			$newvar['nname'] = $c;

			$v = str_replace("\n\n", "\n", $v);

			$v = str_replace(".mr", ".mr ----------", $v);

			$newvar['description'] = "---------- * " . str_replace("\n", "<br />", $v);

			$result[] = $newvar;
		}

		return $result;
	}

	static function initThisList($parent, $class)
	{

		global $gbl, $sgbl, $login, $ghtml;

		$list = getFullVersionList();

		$result = self::parseReleaseNote($list);

		return $result;
	}
}
