<?php 

include_once "lib/html/include.php"; 

initProgram('admin');

$login->loadAllObjects('client');
$list = $login->getList('client');

$clist = array();

$nolog = false;

$ttype = 'a';
$hostname = 'stats';

log_cleanup("*** Add DNS record for '{$hostname}' key in '{$ttype}' type ***", $nolog);

foreach($list as $c) {
	if ($client) {
		$ca = explode(",", $client);

		if (!in_array($c->nname, $ca)) { continue; }
	}

	$dlist = $c->getList('domain');

	foreach($dlist as $l) {
		$dns = $l->getObject('dns');
		$dns->setUpdateSubaction('full_update');

		print("- For '{$dns->nname}' ('{$c->nname}') at '{$c->syncserver}'\n");

		$added = true;
		$param = '';

		foreach($dns->dns_record_a as $drec) {
			if (($drec->ttype === $ttype) && ($drec->hostname === $hostname)) {
				print("  * already exists of '{$hostname}' key in '{$ttype}' type\n");
				$added = false;
			}

			if (($drec->ttype === $ttype) && ($drec->hostname === '__base__')) {
				$param = $drec->param;
			}
		}

		if ($added) {
			print("  * add '{$hostname}' key in '{$ttype}' type with '{$param}' param\n");

			$dns->addRec($ttype, $hostname, $param);
		}

		$dns->was();
	}
}

