<?php 

include_once "lib/html/include.php"; 

initProgram('admin');

$login->loadAllObjects('client');
$list = $login->getList('client');

$par = parse_opt($argv);

$newip = null;

if (isset($par['oldip'])) {
	$oldip = $par['oldip'];
}

if (isset($par['newip'])) {
	$newip = $par['newip'];
}

log_cleanup("*** Changing DNS 'A record' IP ***", $nolog);

foreach($list as $c) {
	$dlist = $c->getList('domain');

	foreach($dlist as $l) {
		$dns = $l->getObject('dns');
		$dns->setUpdateSubaction('full_update');

		$changed = false;

		if ($newip && $oldip) {
			print("- For '{$dns->nname}' ('{$c->nname}') at '{$c->syncserver}'\n");

			foreach($dns->dns_record_a as $drec) {
				if ($drec->ttype !== 'a') {
					continue;
				}

				if ($drec->param === $oldip) {
					$sub = str_replace("a_", "", $drec->nname);

					print("  * old '{$oldip}' to new '{$newip}' for '{$sub}'\n");

					$drec->param = $newip;

					$changed = false;
				}
			}
		}

		if ($changed === false) {
			print("  * no exists of old '{$oldip}' for '{$dns->nname}'\n");
		}

		$dns->was();
	}
}

