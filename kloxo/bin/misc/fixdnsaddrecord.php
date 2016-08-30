<?php 

include_once "lib/html/include.php"; 

initProgram('admin');

$login->loadAllObjects('client');
$list = $login->getList('client');

$par = parse_opt($argv);

if (isset($par['type'])) {
	$ttype = strtolower($par['type']);
}

if (isset($par['key'])) {
	$hostname = strtolower($par['key']);
}

if (isset($par['value'])) {
	$param = strtolower($par['value']);
}

$client = (isset($par['client'])) ? $par['client'] : null;
$clist = array();

$nolog = false;

log_cleanup("Add DNS record for '{$hostname}' key in '{$ttype}' type", $nolog);

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

		foreach($dns->dns_record_a as $drec) {
			if (($drec->ttype === $ttype) && ($drec->hostname === $hostname)) {
				print("-- already exists of '{$hostname}' key in '{$ttype}' type\n");
				$added = false;
			}
		}

		if ($added) {
			print("-- add '{$hostname}' key in '{$ttype}' type\n");

			$dns->dns_record_a->addRec($ttype, $hostname, $param);
		}

		$dns->was();
	}
}

