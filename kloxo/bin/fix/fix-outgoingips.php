<?php 

include_once "lib/html/include.php"; 

initProgram('admin');

$login->loadAllObjects('client');
$list = $login->getList('client');

log_cleanup("Fixing Mail Outgoing IP", $nolog);

$t = '';

foreach($list as $c) {
	$dlist = $c->getList('domain');

	foreach($dlist as $l) {
		$dns = $l->getObject('dns');

		foreach($dns->dns_record_a as $drec) {
			if (($drec->ttype === 'a') && ($drec->hostname === '__base__')) {
				print("- For '{$dns->nname}' domain ('{$c->nname}' client) at '{$c->syncserver}' server\n");
				$t .= "{$dns->nname}:{$drec->param}\n";
			}
		}

		$dns->was();
	}
	
	file_put_contents("/var/qmail/control/outgoingips", $t);
}

