<?php 

include_once "lib/html/include.php"; 
initProgram('admin');

$server = (isset($list['server'])) ? $list['server'] : 'localhost';
$client = (isset($list['client'])) ? $list['client'] : null;
$domain = (isset($list['domain'])) ? $list['domain'] : null;
$nolog  = (isset($list['nolog'])) ? $list['nolog'] : null;


$login->loadAllObjects('client');
$list = $login->getList('client');

foreach($list as $c) {
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

	foreach((array) $dlist as $l) {
		if ($domain) {
			$da = explode(",", $domain);
			if (!in_array($web->nname, $da)) { continue; }
		}

		$l->generateDomainKey(false);
	}
}

