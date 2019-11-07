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

$domain = (isset($par['domain'])) ? $par['domain'] : null;
$domlist = array();

$nolog = false;

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

		if ($domain) {
			$da = explode(",", $domain);

			if (!in_array($dns->nname, $da)) { continue; }
		}

		print("- For '{$dns->nname}' ('{$c->nname}') at '{$c->syncserver}'\n");

		$added = true;

		foreach($dns->dns_record_a as $drec) {
			if (($drec->ttype === $ttype) && ($drec->hostname === $hostname)) {
				print("  * already exists of '{$hostname}' key in '{$ttype}' type\n");
				$added = false;
			}
		}

		if ($added) {
			print("  * add '{$hostname}' key in '{$ttype}' type with '{$param}' param\n");

			$dns->addRec($ttype, $hostname, $param);
		}

		$dns->was();
	}
}

