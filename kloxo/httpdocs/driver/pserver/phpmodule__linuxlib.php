<?php

class PhpModule__linux extends lxDriverClass
{
	static function getListDetail($syncserver, $list = null)
	{
		global $sgbl;

		$files = glob("/opt/php*m/etc/php.d/*ini", GLOB_MARK);

		foreach ($files as $fk => $fv) {
			$b = basename($fv);

			$p = preg_replace("(\/opt\/|\/etc\/php\.d\/{$b})", "", $fv);

			$target = $p;

			$n = preg_replace("(\_used|\_unused|\.ini|\.nonini)", "", $b);

			$nname = "{$p}_{$n}___{$syncserver}";

			$modulename = $n;

			if (strpos($b, '.ini') !== false) {
				$status = "on";
			} else {
				$status = "off";
			}

			$t[] = array('nname' => $nname, 'modulename' => $modulename, 
				'target' => $target, 'type' => 'multiple-php', 'status' => $status,
				'fullfile' => $fv);

		}

		$files = glob("/etc/php.d/*ini", GLOB_MARK);

		foreach ($files as $fk => $fv) {
			$b = basename($fv);

			$p = 'php';

			$target = $p;

			$n = preg_replace("(\_used|\_unused|\.ini|\.nonini)", "", $b);

			$nname = "{$p}-{$n}___{$syncserver}";

			$modulename = $n;

			if (strpos($b, '.ini') !== false) {
				$status = "on";
			} else {
				$status = "off";
			}

			$t[] = array('nname' => $nname, 'modulename' => $modulename, 
				'target' => $target, 'type' => 'php-branch', 'status' => $status,
				'fullfile' => $fv);

		}

		return $t;
	}
}