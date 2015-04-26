<?php 

include_once "lib/html/include.php"; 

initProgram('admin');

$login->loadAllObjects('client');
$list = $login->getList('client');

$par = parse_opt($argv);

if (isset($par['ttype'])) {
	$ttype = $par['ttype'];
}

if (isset($par['hostname'])) {
	$hostname = $par['hostname'];
}

$nolog = false;

log_cleanup("Remove DNS record for '{$hostname}' hostname in '{$ttype}' ttype", $nolog);

foreach($list as $c) {
	$dlist = $c->getList('domain');

	foreach($dlist as $l) {
		$dns = $l->getObject('dns');
		$dns->setUpdateSubaction('full_update');

		print("- For '{$dns->nname}' ('{$c->nname}') at '{$c->syncserver}'\n");

		foreach($dns->dns_record_a as $drec) {
			if (($drec->ttype === $ttype) && ($drec->hostname === $hostname)) {
				print("-- remove '{$drec->hostname}' hostname in '{$drec->ttype}'\n");
			} else {
				$x[] = $drec;
			}
		}

		$dns->dns_record_a = $x;

		$dns->was();
	}
}

