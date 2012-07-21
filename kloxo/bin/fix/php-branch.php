<?php
// release by mustafa.ramadhan@lxcenter.org

include_once "htmllib/lib/include.php";

$list = parse_opt($argv);

$select = (isset($list['select'])) ? $list['select'] : '';
$nolog  = (isset($list['nolog']))  ? $list['nolog'] : null;

setPhpBranch($select, $nolog);

function setPhpBranch($select, $nolog = null)
{
	$phpbranch = getPhpBranch();

	if ($select === $phpbranch) {
		print("\nIt's the same branch ({$select}); no changed.\n");
		return null;
	} elseif ($select === '') {
		print("\nIt's no select entry.\n");
		return null;
	} else {
		// check 'yum-plugin-replace' installed or not

		$yumreplace = 'yum-plugin-replace';

		if (!isRpmInstalled($yumreplace)) {
			$ret = lxshell_return("yum", "-y", "install", $yumreplace);

			if ($ret) {
				throw new lxException("{$yumreplace}_install_failed", '', 'parent');
			}
		}

		$ret = lxshell_return("yum", "-y", "replace", $phpbranch, "--replace-with={$select}");

		if ($ret) {
			throw new lxException("change_to_{$select}_branch_failed", '', 'parent');
		}

	}

	exec("sh /script/fixphp");
	exec("sh /script/fixweb");

	createRestartFile('phpfpm');
}
