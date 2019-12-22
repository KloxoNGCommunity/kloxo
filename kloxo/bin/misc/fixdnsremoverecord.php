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

$client = (isset($par['client'])) ? $par['client'] : null;
$clist = array();

$domain = (isset($par['domain'])) ? $par['domain'] : null;
$domlist = array();

$nolog = false;

log_cleanup("*** Remove DNS record for '{$hostname}' key in '{$ttype}' type ***", $nolog);

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

		$removed = false;

		foreach($dns->dns_record_a as $drec) {
			if (($drec->ttype === $ttype) && ($drec->hostname === $hostname)) {
				print("  * remove '{$drec->hostname}' key in '{$drec->ttype}' type\n");
				$removed = true;
			} else {
				$x[] = $drec;
			}
		}

		if ($removed === false) {
			print("  * nO exists of '{$hostname}' key in '{$ttype}' type\n");
		}

		$dns->dns_record_a = $x;

		$dns->was();
	}
}

