<?php 

include_once "lib/html/include.php"; 
initProgram('admin');

$list = parse_opt($argv);

$server = (isset($list['server'])) ? $list['server'] : 'localhost';
$client = (isset($list['client'])) ? $list['client'] : null;
$nolog  = (isset($list['nolog'])) ? $list['nolog'] : null;

log_cleanup("Fixing DNS server config (including their parked/redirect domains)", $nolog);

if (isset($list['new_dnstemplate'])) {
	$dnst = new Dnstemplate(null, null, $list['new_dnstemplate']);
	$dnst->get();

	if ($dnst->dbaction === 'add') {
		log_cleanup("- DNS template doesn't exist", $nolog);

		exit;
	}
}

$login->loadAllObjects('client');
$clist = $login->getList('client');

foreach($clist as $c) {
/*
	$driverapp = $gbl->getSyncClass(null, $c->syncserver, 'dns');

	if ($driverapp === 'none') {
		log_cleanup("- No process because using 'NONE' driver for '{$c->syncserver}'", $nolog);

		return;
	}
*/
	if ($client) {
		$ca = explode(",", $client);
		if (!in_array($c->nname, $ca)) { continue; }
		$server = 'all';
	}

	if ($server !== 'all') {
		$sa = explode(",", $server);
		if (!in_array($c->syncserver, $sa)) { continue; }
	}

	$dlist = $c->getList('domaina');

	if (!$dlist) { continue; }
	
	$cc = $c;

	$counter = 0;

	foreach($dlist as $l) {
		$counter++;

		$dns = $l->getObject('dns');

		if (isset($dnst)) {
			$dns->dns_record_a = null;
			$dns->copyObject($dnst);
		}

		log_cleanup("- '{$dns->nname}' ('{$c->nname}') at '{$dns->syncserver}'", $nolog);
	//	$dns->setUpdateSubaction('full_update');
		$dns->setUpdateSubaction('domain');

		// MR -- only after latest domains per-client; faster process!
		if (sizeof($dlist) === $counter) {
			$dns->setUpdateSubaction('synchronize_fix');
			$dns->setUpdateSubaction('allowed_transfer');
		}

		$dns->was();
	}
}
